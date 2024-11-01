<?php declare(strict_types=1);

namespace Yoyaku\Application\EventType\EventBooking;

use WP_Error;
use Yoyaku\Application\Common\Exceptions\NotAllowedError;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\Customer\Birthday;
use Yoyaku\Domain\Customer\Customer;
use Yoyaku\Domain\Customer\Gender;
use Yoyaku\Domain\Customer\ZipCode;
use Yoyaku\Domain\DateTime\DateTimeService;
use Yoyaku\Domain\EventType\Event\Event;
use Yoyaku\Domain\EventType\EventBooking\BookingStatus;
use Yoyaku\Domain\EventType\EventBooking\EventBooking;
use Yoyaku\Domain\EventType\EventPeriod\EventPeriod;
use Yoyaku\Domain\EventType\EventTicket\BuyEventTicket;
use Yoyaku\Domain\EventType\EventTicket\BuyEventTicketCollection;
use Yoyaku\Domain\EventType\EventTicket\BuyEventTicketFactory;
use Yoyaku\Domain\EventType\EventTicket\EventTicket;
use Yoyaku\Domain\ValueObject\String\Address;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Domain\ValueObject\String\Phone;
use Yoyaku\Infrastructure\Repository\EventType\EventBookingRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventTicketBookingRepository;

/**
 *
 */
class EventBookingApplicationService
{
    private EventBookingRepository $booking_repo;
    private EventTicketBookingRepository $ticket_booking_repo;

    /**
     * @param EventBookingRepository $booking_repo
     * @param EventTicketBookingRepository $ticket_booking_repo
     */
    public function __construct(
        EventBookingRepository       $booking_repo,
        EventTicketBookingRepository $ticket_booking_repo,
    )
    {
        $this->booking_repo = $booking_repo;
        $this->ticket_booking_repo = $ticket_booking_repo;
    }


    /**
     * @param Collection<EventTicket> $tickets
     * @param $tickets_param
     * @return WP_Error|BuyEventTicketCollection<BuyEventTicket>
     */
    public function get_buy_event_ticket_collection($tickets, $tickets_param)
    {
        // 購入チケットとフェッチしたチケットの同一チェック
        $ticket_ids = array_column($tickets->to_array(), 'id');
        $buy_ticket_ids = array_column($tickets_param, 'id');
        if (count($buy_ticket_ids) != count($ticket_ids)) {
            return new WP_Error(
                400,
                __('Selected ticket does not exist.', 'yoyaku-manager'),
                ['status' => 400]
            );
        }

        // keyがチケットid、valueがチケットデータの連想配列を作成
        $buy_tickets_param = [];
        foreach ($tickets_param as $ticket) {
            $buy_tickets_param[$ticket['id']] = $ticket;
        }

        $buy_event_tickets = new BuyEventTicketCollection();
        /** @var EventTicket $ticket */
        foreach ($tickets->get_items() as $ticket) {
            $ticket_id = $ticket->get_id()->get_value();
            $buy_event_tickets->add_item(
                BuyEventTicketFactory::create(
                    [
                        'id' => $ticket_id,
                        'name' => $ticket->get_name()->get_value(),
                        'ticket_count' => $ticket->get_ticket_count()->get_value(),
                        'buy_count' => $buy_tickets_param[$ticket_id]['buy_count'],
                        'price' => $ticket->get_price()->get_value(),
                    ]
                ),
                $ticket_id
            );
        }

        return $buy_event_tickets;
    }

    /**
     * 購入チケットの妥当性検証
     * @param EventPeriod $event_period
     * @param BuyEventTicketCollection<BuyEventTicket> $buy_tickets
     * @return true|WP_Error
     * @throws WpDbException
     */
    public function validate_ticket_booking($event_period, $buy_tickets)
    {
        // 各チケットの残り枚数チェック
        $event_period_id = $event_period->get_id()->get_value();
        $ticket_ids = array_column($buy_tickets->to_array(), 'id');
        $ticket_id_sold_count_map
            = $this->ticket_booking_repo->get_sold_ticket_count_list($event_period_id, $ticket_ids);
        $total_ticket_count = 0;
        foreach ($buy_tickets->get_items() as $buy_ticket) {
            $ticket_id = $buy_ticket->get_id()->get_value();
            $sold = $ticket_id_sold_count_map[$ticket_id] + $buy_ticket->get_buy_count()->get_value();
            $total_ticket_count += $sold;

            if ($buy_ticket->get_ticket_count()->get_value() < $sold) {
                return new WP_Error(
                    400,
                    sprintf(
                    /* translators: %s ticket name */
                        __('%s is sold out. Please enter it again.', 'yoyaku-manager'),
                        $buy_ticket->get_name()->get_value()
                    ),
                    ['status' => 400]
                );
            }
        }

        // チケットの販売済み枚数+購入枚数　が イベント期間のチケット枚数上限 を超えていないかチェック
        if ($event_period->get_max_ticket_count()->get_value() < $total_ticket_count) {
            return new WP_Error(409, __('This event is full.', 'yoyaku-manager'), ['status' => 409]);
        }

        return true;
    }

    /**
     * 予約を承認する
     * @param EventBooking $booking
     * @throws WpDbException
     */
    public function approve($booking)
    {
        if ($booking->get_status() == BookingStatus::APPROVED) {
            return;
        }

        $this->booking_repo->update(
            ['status' => BookingStatus::APPROVED->value],
            ['id' => $booking->get_id()->get_value()]
        );
    }

    /**
     * @param EventBooking $booking
     * @param bool $is_front
     * @param Event|null $event
     * @param EventPeriod|null $event_period
     * @throws NotAllowedError
     * @throws WpDbException
     */
    public function cancel($booking, $is_front, $event = null, $event_period = null)
    {
        $now = DateTimeService::get_now_datetime_object();
        if ($is_front) {
            assert($event && $event_period);
            // キャンセル受付締切り日時を過ぎていないかチェック
            if (!$event_period->can_cancel_now($event->get_min_time_to_cancel_booking(), $now)) {
                throw new NotAllowedError(esc_html__('It is past cancellation due date.', 'yoyaku-manager'));
            }
        }

        if ($booking->get_status() == BookingStatus::CANCELED) {
            throw new NotAllowedError(esc_html__('This booking has already been canceled.', 'yoyaku-manager'));
        } elseif ($booking->get_status() == BookingStatus::DISAPPROVED) {
            throw new NotAllowedError(esc_html__('This booking has already been disapproved.', 'yoyaku-manager'));
        }

        $this->booking_repo->update(
            ['status' => BookingStatus::CANCELED->value],
            ['id' => $booking->get_id()->get_value()]
        );
    }

    /**
     * 登録されている顧客データと入力された顧客データが異なるかチェックする（メールアドレス以外）
     * 異なっていたら $customer のデータを更新する
     * @param array $params 顧客のパラメーター
     * @param Customer $customer
     * @return bool
     */
    public function update_customer_obj($params, $customer)
    {
        $result = false;
        if ($params['first_name'] != $customer->get_first_name()->get_value()) {
            $customer->set_first_name(new Name($params['first_name']));
            $result = true;
        }

        if ($params['last_name'] != $customer->get_last_name()->get_value()) {
            $customer->set_last_name(new Name($params['last_name']));
            $result = true;
        }

        // 電話番号や、郵便番号、住所は未使用または、任意項目の可能性もあるため、入力値が空でなければ更新する
        if (isset($params['first_name_ruby'])
            && $params['first_name_ruby'] != $customer->get_first_name_ruby()->get_value()
        ) {
            $customer->set_first_name_ruby(new Name($params['first_name_ruby']));
            $result = true;
        }

        if (isset($params['last_name_ruby'])
            && $params['last_name_ruby'] != $customer->get_last_name_ruby()->get_value()
        ) {
            $customer->set_last_name_ruby(new Name($params['last_name_ruby']));
            $result = true;
        }

        if (isset($params['phone']) && $params['phone'] != $customer->get_phone()->get_value()) {
            $customer->set_phone(new Phone($params['phone']));
            $result = true;
        }

        if (isset($params['zipcode']) && $params['zipcode'] != $customer->get_zipcode()->get_value()) {
            $customer->set_zipcode(new ZipCode($params['zipcode']));
            $result = true;
        }

        if (isset($params['address']) && $params['address'] != $customer->get_address()->get_value()) {
            $customer->set_address(new Address($params['address']));
            $result = true;
        }

        if (isset($params['birthday']) && $params['birthday'] != $customer->get_birthday()?->get_value()) {
            $customer->set_birthday(new Birthday($params['birthday']));
            $result = true;
        }

        if (isset($params['gender']) && $params['gender'] != $customer->get_gender()) {
            $customer->set_gender(Gender::tryFrom($params['gender']));
            $result = true;
        }

        return $result;
    }
}

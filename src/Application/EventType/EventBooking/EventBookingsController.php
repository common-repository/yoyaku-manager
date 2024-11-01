<?php declare(strict_types=1);

namespace Yoyaku\Application\EventType\EventBooking;

use Exception;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use Yoyaku\Application\Common\AController;
use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\Common\Exceptions\NotAllowedError;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Application\Common\ServerError;
use Yoyaku\Application\Notification\EmailApplicationService;
use Yoyaku\Application\Payment\PaymentApplicationService;
use Yoyaku\Domain\Customer\Customer;
use Yoyaku\Domain\Customer\CustomerService;
use Yoyaku\Domain\DateTime\DateTimeService;
use Yoyaku\Domain\EventType\Event\Event;
use Yoyaku\Domain\EventType\EventBooking\BookingStatus;
use Yoyaku\Domain\EventType\EventBooking\EventBooking;
use Yoyaku\Domain\EventType\EventBooking\EventBookingFactory;
use Yoyaku\Domain\EventType\EventBookingPlaceholdersData;
use Yoyaku\Domain\EventType\EventPeriod\EventPeriod;
use Yoyaku\Domain\EventType\EventTicket\BuyEventTicketCollection;
use Yoyaku\Domain\EventType\EventTicket\BuyEventTicketFactory;
use Yoyaku\Domain\EventType\EventTicketBooking\EventTicketBookingService;
use Yoyaku\Domain\Payment\GatewayType;
use Yoyaku\Domain\Payment\PaymentStatus;
use Yoyaku\Domain\Setting\OptionFieldStatus;
use Yoyaku\Domain\Setting\SettingsService;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\Number\Price;
use Yoyaku\Domain\ValueObject\String\Email;
use Yoyaku\Infrastructure\Repository\Customer\CustomerRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventBookingRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventPeriodRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventTicketBookingRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventTicketRepository;
use Yoyaku\Infrastructure\Services\ReCaptchaService;
use Yoyaku\Infrastructure\WP\DB;

class EventBookingsController extends AController
{
    private EventBookingApplicationService $booking_as;
    private PaymentApplicationService $payment_as;
    private CustomerService $customer_ds;
    private EventTicketBookingService $ticket_booking_ds;
    private CustomerRepository $customer_repo;
    private EventRepository $event_repo;
    private EventPeriodRepository $period_repo;
    private EventTicketRepository $ticket_repo;
    private EventBookingRepository $booking_repo;
    private EventTicketBookingRepository $ticket_booking_repo;
    private EmailApplicationService $email_as;

    public function __construct(
        $event_booking_as,
        $payment_as,
        $customer_ds,
        $ticket_booking_ds,
        $customer_repo,
        $event_repo,
        $event_period_repo,
        $booking_repo,
        $ticket_booking_repo,
        $email_as,
    )
    {
        $this->booking_as = $event_booking_as;
        $this->payment_as = $payment_as;
        $this->customer_ds = $customer_ds;
        $this->ticket_booking_ds = $ticket_booking_ds;
        $this->customer_repo = $customer_repo;
        $this->event_repo = $event_repo;
        $this->period_repo = $event_period_repo;
        $this->booking_repo = $booking_repo;
        $this->ticket_booking_repo = $ticket_booking_repo;
        $this->email_as = $email_as;

        $this->ticket_repo = new EventTicketRepository();
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_items($request)
    {
        try {
            $params = $request->get_params();
            $items = $this->booking_repo->filter($params);
            $count = $this->booking_repo->filter($params, true);
            $per_page = $request->get_param('per_page');
            return new WP_REST_Response([
                'items' => $items->to_array(),
                'num_pages' => $per_page ? intval(ceil($count / $per_page)) : 1,
                'total' => $count,
            ]);

        } catch (WpDbException $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * 管理画面から追加する場合は承認制でも、即承認する
     * EventFired の処理に失敗したらロールバックする
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     * @throws Exception
     */
    public function add_item($request)
    {
        $tickets_param = $request->get_param('tickets');
        $is_valid = $this->validate_tickets_param($tickets_param);
        if (is_wp_error($is_valid)) {
            return $is_valid;
        }
        $customer_params = $this->get_customer_params($request);

        try {
            $event_period = $this->period_repo->get_by_id($request->get_param('event_period_id'));
            $event_id = $event_period->get_event_id()->get_value();
            $event = $this->event_repo->get_by_id($event_id);
            $tickets = $this->ticket_repo->filter(
                ['event_id' => $event_id, 'id__in' => array_column($tickets_param, 'id')]
            );
            $buy_tickets = $this->booking_as->get_buy_event_ticket_collection($tickets, $tickets_param);
            if (is_wp_error($buy_tickets)) {
                return $buy_tickets;
            }

            DB::begin();
            $add_customer = false;
            try {
                $customer = $this->customer_repo->get_by_email($request->get_param('email'), true);
                $customer_id = $customer->get_id()->get_value();
                if ($this->booking_repo->is_exist($customer_id, $event_period->get_id()->get_value())) {
                    return new WP_Error(
                        400,
                        __('You are already booked.', 'yoyaku-manager'),
                        ['status' => 400]
                    );
                }
            } catch (DataNotFoundException) {
                $add_customer = true;
            }

            if ($add_customer) {
                $customer_id = $this->customer_ds->add($customer_params);
            } else if ($this->booking_as->update_customer_obj($customer_params, $customer)) {
                $this->customer_repo->update_by_entity($customer_id, $customer);
            }

            // 予約追加
            $booking = EventBookingFactory::create(
                array_merge(
                    $request->get_params(),
                    [
                        'event_period_id' => $event_period->get_id()->get_value(),
                        'customer_id' => $customer_id,
                        'status' => BookingStatus::APPROVED->value,
                        'gateway' => $request->get_param('gateway'),
                        'amount' => $buy_tickets->get_amount(),
                        'created' => DateTimeService::get_now_datetime_in_utc(),
                    ]
                )
            );
            $booking_id = $this->booking_repo->add_by_entity($booking);
            $this->ticket_booking_ds->bulk_add($booking_id, $buy_tickets);

            DB::commit();

            $booking->set_id(new Id($booking_id));
            $this->email_as->send_booking_notification(
                new EventBookingPlaceholdersData($event, $event_period, $booking, $buy_tickets),
                $event->get_id()->get_value()
            );

            return new WP_REST_Response(['id' => $booking_id]);

        } catch (NotAllowedError|DataNotFoundException $e) {
            DB::rollback();
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);

        } catch (WpDbException $e) {
            DB::rollback();
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     * @throws WpDbException
     */
    public function get_item($request)
    {
        try {
            $id = $request->get_param('id');
            $booking = $this->booking_repo->get_by_id($id)->to_array();
            $booking['tickets'] = $this->ticket_booking_repo->filter_by_event_booking_id($id)->to_array();
            return new WP_REST_Response($booking);

        } catch (DataNotFoundException $e) {
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);
        }
    }

    /**
     * 予約データを更新。ただし、チケットやgatewayの変更はできない
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     * @throws Exception
     */
    public function update_item($request)
    {
        $tickets_param = $request->get_param('tickets');
        $is_valid = $this->validate_tickets_param($tickets_param);
        if (is_wp_error($is_valid)) {
            return $is_valid;
        }

        try {
            DB::begin();
            $id = $request->get_param('id');
            /** @var EventBooking $booking */
            $booking = $this->booking_repo->get_by_id($id);

            // 顧客を更新する
            if ($request->get_param('update_customer')) {
                $customer_params = $this->get_customer_params($request);
                /** @var Customer $customer */
                $customer = $this->customer_repo->get_by_id($booking->get_customer_id()->get_value());

                $email = new Email($customer_params['email']);
                $is_updated = $customer->get_email()->get_value() !== $email->get_value();
                if ($is_updated) {
                    if (!$this->customer_ds->can_add($email)) {
                        return new WP_Error(
                            400,
                            __('This email is already in use.', 'yoyaku-manager'),
                            ['status' => 400]
                        );
                    }
                    $customer->set_email($email);
                }

                if ($this->booking_as->update_customer_obj($customer_params, $customer) || $is_updated) {
                    $this->customer_repo->update_by_entity($customer->get_id()->get_value(), $customer);
                }
            }

            // 予約を更新する
            $new_entity = EventBookingFactory::create(array_merge($booking->to_array(), $request->get_params()));
            $this->booking_repo->update_by_entity($id, $new_entity);

            // 予約チケットデータ 洗い替え
            $buy_tickets = new BuyEventTicketCollection();
            foreach ($tickets_param as $ticket) {
                $buy_tickets->add_item(
                    BuyEventTicketFactory::create(
                        [
                            'id' => $ticket['id'],
                            'buy_count' => $ticket['buy_count'],
                        ]
                    ),
                    $ticket['id']
                );
            }
            $this->ticket_booking_repo->delete(['event_booking_id' => $id]);
            $this->ticket_booking_ds->bulk_add($id, $buy_tickets);

            DB::commit();
            return new WP_REST_Response(['id' => $id]);

        } catch (DataNotFoundException $e) {
            DB::rollback();
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);

        } catch (WpDbException $e) {
            DB::rollback();
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function delete_item($request)
    {
        try {
            $this->booking_repo->delete(['id' => $request->get_param('id')]);
            return new WP_REST_Response(['message' => 'success']);

        } catch (WpDbException $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * pendingに変更された場合の通知はない
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function update_item_status($request)
    {
        $send_mail = false;
        $id = $request->get_param('id');
        $status = BookingStatus::tryFrom($request->get_param('status'));
        try {
            DB::begin();
            /** @var EventBooking $booking */
            $booking = $this->booking_repo->get_by_id($id);
            if ($status == BookingStatus::APPROVED) {
                $send_mail = true;
                $this->booking_as->approve($booking);
            } else if ($status == BookingStatus::CANCELED) {
                $send_mail = true;
                $this->booking_as->cancel($booking, false);
            } else if ($status == BookingStatus::DISAPPROVED) {
                $send_mail = true;
                $this->booking_repo->update(['status' => $status->value], ['id' => $id]);
            }

            if ($send_mail) {
                $booking->set_status($status);
                /** @var EventPeriod $event_period */
                $event_period = $this->period_repo->get_by_id($booking->get_event_period_id()->get_value());
                $event = $this->event_repo->get_by_id($event_period->get_event_id()->get_value());
                $buy_tickets = $this->ticket_booking_repo->filter_by_event_booking_id($id);
                try {
                    $this->email_as->send_booking_notification(
                        new EventBookingPlaceholdersData($event, $event_period, $booking, $buy_tickets),
                        $event->get_id()->get_value()
                    );
                } catch (\PHPMailer\PHPMailer\Exception $e) {
                }
            }
            DB::commit();
            return new WP_REST_Response(['message' => $status->value]);

        } catch (NotAllowedError|DataNotFoundException $e) {
            DB::rollback();
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);

        } catch (WpDbException $e) {
            DB::rollback();
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * apiのargsに動的にrequiredを設定するとテストが通らないため callback 内でチェックする
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error イベント期間選択ページに戻る必要がある場合は409を返す
     * @throws Exception
     */
    public function front_add_item($request)
    {
        $tickets_param = $request->get_param('tickets');
        $is_valid = $this->validate_tickets_param($tickets_param);
        if (is_wp_error($is_valid)) {
            return $is_valid;
        }

        $customer_params = $this->get_customer_params($request, true);
        if (is_wp_error($customer_params)) {
            return $customer_params;
        }

        $settings = SettingsService::get_instance();
        $secret = $settings->get('google_recaptcha_secret_key');
        if ($secret) {
            $resp = ReCaptchaService::verify($secret, $request->get_param('captcha_value'));
            if (!$resp->isSuccess()) {
                $msg = 'reCAPTCHA Error: error_codes[' . implode(' ', $resp->getErrorCodes()) . ']';
                return new WP_Error(400, $msg, ['status' => 400]);
            }
        }

        try {
            $event_period = $this->period_repo->get_by_uuid($request->get_param('event_period_uuid'));
            $event_id = $event_period->get_event_id()->get_value();
            /** @var Event $event */
            $event = $this->event_repo->get_by_id($event_id);
            $tickets = $this->ticket_repo->filter(
                ['event_id' => $event_id, 'id__in' => array_column($tickets_param, 'id')]
            );
            $buy_tickets = $this->booking_as->get_buy_event_ticket_collection($tickets, $tickets_param);
            if (is_wp_error($buy_tickets)) {
                return $buy_tickets;
            }
            $validate_result = $this->booking_as->validate_ticket_booking($event_period, $buy_tickets);
            if (is_wp_error($validate_result)) {
                return $validate_result;
            }

            // 予約受付締切チェック
            $now = DateTimeService::get_now_datetime_object();
            if (!$event_period->can_book_now($event->get_min_time_to_close_booking(), $now)) {
                return new WP_Error(
                    409,
                    __('This event has ended.', 'yoyaku-manager'),
                    ['status' => 409]
                );
            }

            // 購入上限数チェック useFormでもチェックしているため通常起こり得ないが、
            // 予約フォームに入力中に購入上限数を変更すると発生し得る。
            $buy_tickets_total = array_sum(array_column($tickets_param, 'buy_count'));
            $max = $event->get_max_tickets_per_booking()->get_value();
            if ($max < $buy_tickets_total) {
                return new WP_Error(
                    409,
                    sprintf(
                    /* translators: %d is replaced with "number" */
                        __('Exceeds the upper limit. Please keep the total below %d.', 'yoyaku-manager'),
                        $max
                    ),
                    ['status' => 409]
                );
            }

            DB::begin();
            $add_customer = false;
            try {
                $customer = $this->customer_repo->get_by_email($request->get_param('email'), true);
                $customer_id = $customer->get_id()->get_value();
                if ($this->booking_repo->is_exist($customer_id, $event_period->get_id()->get_value())) {
                    return new WP_Error(
                        400,
                        __('You are already booked.', 'yoyaku-manager'),
                        ['status' => 400]
                    );
                }
            } catch (DataNotFoundException) {
                $add_customer = true;
            }

            // 支払い処理
            $gateway = GatewayType::tryFrom($request->get_param('gateway'));
            try {
                $response = $this->payment_as->process_payment(
                    $gateway,
                    $request->get_param('confirmation_token_id'),
                    new Price($buy_tickets->get_amount()),
                );
            } catch (Exception $e) {
                DB::rollback();
                return new WP_Error(
                    400,
                    __('Payment failed.', 'yoyaku-manager') . $e->getMessage(),
                    ['status' => 400]
                );
            }

            if ($add_customer) {
                $customer_id = $this->customer_ds->add($customer_params);
            } else if ($this->booking_as->update_customer_obj($customer_params, $customer)) {
                $this->customer_repo->update_by_entity($customer_id, $customer);
            }

            // 予約追加
            $booking = EventBookingFactory::create(
                array_merge(
                    $request->get_params(),
                    [
                        'customer_id' => $customer_id,
                        'event_period_id' => $event_period->get_id()->get_value(),
                        'status' => $event->get_use_approval_system() ? BookingStatus::PENDING->value : BookingStatus::APPROVED->value,
                        'gateway' => $gateway->value,
                        'amount' => $buy_tickets->get_amount(),
                        'payment_status' => ($gateway != GatewayType::ON_SITE || $buy_tickets->get_amount() == 0.0)
                            ? PaymentStatus::PAID->value : PaymentStatus::PENDING->value,
                        'created' => DateTimeService::get_now_datetime_in_utc(),
                    ]
                )
            );
            $booking->set_transaction_id($response->get_transaction_id());
            $booking_id = $this->booking_repo->add_by_entity($booking);
            $this->ticket_booking_ds->bulk_add($booking_id, $buy_tickets);

            DB::commit();

            $booking->set_id(new Id($booking_id));
            $this->email_as->send_booking_notification(
                new EventBookingPlaceholdersData($event, $event_period, $booking, $buy_tickets),
                $event->get_id()->get_value()
            );

            return new WP_REST_Response(['redirect_url' => $event->get_redirect_url()->get_value()]);

        } catch (DataNotFoundException $e) {
            DB::rollback();
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);

        } catch (WpDbException $e) {
            DB::rollback();
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function front_cancel_item($request)
    {
        try {
            DB::begin();
            $token = $request->get_param('token');
            $booking_query_service = new EventBookingQuery();
            [$event, $event_period, $booking] = $booking_query_service->get_placeholder_data_by_token($token);
            $this->booking_as->cancel($booking, true, $event, $event_period);
            $booking->set_status(BookingStatus::CANCELED);
            DB::commit();

            $buy_tickets = $this->ticket_booking_repo->filter_by_event_booking_id($booking->get_id()->get_value());
            try {
                $this->email_as->send_booking_notification(
                    new EventBookingPlaceholdersData($event, $event_period, $booking, $buy_tickets),
                    $event->get_id()->get_value()
                );
            } catch (\PHPMailer\PHPMailer\Exception $e) {
            }

            return new WP_REST_Response(['message' => 'success']);

        } catch (NotAllowedError|DataNotFoundException $e) {
            DB::rollback();
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);

        } catch (WpDbException $e) {
            DB::rollback();
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * 顧客のパラメーターから必須の値、または任意項目で入力されている値を取得
     * @param WP_REST_Request $request
     * @param bool $check_required_fields required_phone_field などカスタムできる顧客情報をチェックをするか否か
     * @return array|WP_Error 顧客のパラメーター（設定に応じて戻り値の配列のキーが変わる）
     */
    private function get_customer_params($request, $check_required_fields = false)
    {
        $settings = SettingsService::get_instance();

        if ($check_required_fields) {
            $error_fields = [];
            if ($settings->get("ruby_field_status") === OptionFieldStatus::REQUIRED->value
                && (
                    is_null($request->get_param('first_name_ruby'))
                    || is_null($request->get_param('last_name_ruby'))
                )
            ) {
                $error_fields[] = 'first_name_ruby, last_name_ruby';
            }

            foreach (['phone', 'birthday', 'address', 'zipcode', 'gender'] as $field) {
                if ($settings->get("{$field}_field_status") === OptionFieldStatus::REQUIRED->value
                    && is_null($request->get_param($field))
                ) {
                    $error_fields[] = $field;
                }
            }

            if ($error_fields) {
                $message = 'Missing parameter(s): ' . implode(', ', $error_fields);
                return new WP_Error(400, $message, ['status' => 400]);
            }
        }

        $result = [
            'email' => $request->get_param('email'),
            'first_name' => $request->get_param('first_name'),
            'last_name' => $request->get_param('last_name'),
        ];

        if ($settings->get('ruby_field_status') !== OptionFieldStatus::HIDDEN->value) {
            $result['first_name_ruby'] = $request->get_param('first_name_ruby');
            $result['last_name_ruby'] = $request->get_param('last_name_ruby');
        }
        foreach (['phone', 'birthday', 'address', 'zipcode', 'gender'] as $field) {
            $value = $request->get_param($field);
            if ($settings->get("{$field}_field_status") !== OptionFieldStatus::HIDDEN->value && !empty($value)) {
                $result[$field] = $value;
            }
        }

        return $result;
    }

    /**
     * 購入するチケットの合計が0枚以上かチェックする
     * @param $tickets_param
     * @return true|WP_Error
     */
    private function validate_tickets_param($tickets_param)
    {
        // 購入するチケットが0枚の場合
        if (0 === array_sum(array_column($tickets_param, 'buy_count'))) {
            return new WP_Error(
                400,
                __('Please select a ticket.', 'yoyaku-manager'),
                ['status' => 400]
            );
        }

        return true;
    }
}

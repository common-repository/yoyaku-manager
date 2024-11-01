<?php declare(strict_types=1);

namespace Yoyaku\Application\Notification;

use Exception;
use Yoyaku\Application\Common\Exceptions\NotAllowedError;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Application\EventType\Event\EventApplicationService;
use Yoyaku\Application\Payment\PaymentApplicationService;
use Yoyaku\Application\Placeholder\PlaceholderApplicationService;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\DateTime\DateTimeService;
use Yoyaku\Domain\EventType\EventBooking\BookingStatus;
use Yoyaku\Domain\EventType\EventBookingPlaceholdersData;
use Yoyaku\Domain\Notification\EmailLogService;
use Yoyaku\Domain\Notification\ScheduledNotificationLogFactory;
use Yoyaku\Domain\Notification\ScheduledNotificationToSend;
use Yoyaku\Infrastructure\Repository\EventType\EventBookingRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventTicketBookingRepository;
use Yoyaku\Infrastructure\Repository\Notification\EmailLogRepository;
use Yoyaku\Infrastructure\Repository\Notification\ScheduledNotificationLogRepository;

class EmailApplicationService extends AEmailApplicationService
{
    private NotificationQuery $notification_query;
    private EventBookingRepository $event_booking_repo;
    private ScheduledNotificationLogRepository $scheduled_notification_log_repo;
    private EventTicketBookingRepository $event_ticket_booking_repo;

    /**
     * @param EventApplicationService $event_as
     * @param PaymentApplicationService $payment_as
     * @param PlaceholderApplicationService $placeholder_as
     * @param NotificationQuery $notification_query
     * @param EmailLogService $email_log_ds
     * @param EmailLogRepository $email_log_repo
     * @param EventBookingRepository $event_booking_repo
     */
    public function __construct(
        EventApplicationService       $event_as,
        PaymentApplicationService     $payment_as,
        PlaceholderApplicationService $placeholder_as,
        NotificationQuery             $notification_query,
        EmailLogService               $email_log_ds,
        EmailLogRepository            $email_log_repo,
        EventBookingRepository        $event_booking_repo,
    )
    {
        parent::__construct(
            $event_as, $payment_as, $placeholder_as, $email_log_ds, $email_log_repo,
        );
        $this->notification_query = $notification_query;
        $this->event_booking_repo = $event_booking_repo;
        $this->scheduled_notification_log_repo = new ScheduledNotificationLogRepository();
        $this->event_ticket_booking_repo = new EventTicketBookingRepository();
    }

    /**
     * 予約の通知
     * @param EventBookingPlaceholdersData $placeholder_data
     * @param $event_id
     * @throws NotAllowedError
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws WpDbException
     */
    public function send_booking_notification($placeholder_data, $event_id)
    {
        $booking_status = $placeholder_data->get_booking_status();
        $notifications = $this->notification_query->filter_by_notification_event(
            ["event_id" => $event_id, "timing" => $booking_status]
        );

        $email_logs = [];
        foreach ($notifications->get_items() as $notification) {
            $email_logs[] = $this->send_notification($notification, $placeholder_data);
        }
        $this->email_log_repo->bulk_add(new Collection($email_logs));
    }

    /**
     * cron を使用して顧客に送信する必要がある開催日の前日のリマインダー通知の配列を返す
     * @throws NotAllowedError
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws WpDbException|Exception
     */
    public function send_scheduled_mails()
    {
        /** @var Collection<ScheduledNotificationToSend> $to_send_list */
        $to_send_list = $this->notification_query->get_to_send_scheduled_notifications();
        $not_send_list = $this->scheduled_notification_log_repo->get_not_send_notifications($to_send_list);

        // イベント期間毎に定期通知を振り分ける
        $grouped_by_event_period = [];
        /** @var ScheduledNotificationToSend $item */
        foreach ($not_send_list->get_items() as $item) {
            $event_period_id = $item->get_event_period_id()->get_value();
            if (!isset($grouped_by_event_period[$event_period_id])) {
                $grouped_by_event_period[$event_period_id] = [];
            }
            $grouped_by_event_period[$event_period_id][] = $item;
        }

        // 各イベント期間の承認済予約者に定期通知を通知する
        foreach ($grouped_by_event_period as $event_period_id => $notification_to_send_list) {
            $bookings = $this->event_booking_repo->filter(
                ['event_period_id' => $event_period_id, 'status' => BookingStatus::APPROVED->value]
            );

            $booking_ids = [];
            foreach ($bookings->get_items() as $booking) {
                $booking_ids[] = $booking->get_id()->get_value();
            }
            $buy_tickets_per_booking = $this->event_ticket_booking_repo->filter_by_event_booking_ids($booking_ids);

            /** @var ScheduledNotificationToSend $notification_to_send */
            foreach ($notification_to_send_list as $notification_to_send) {
                $email_logs = [];
                foreach ($bookings->get_items() as $booking) {
                    $placeholder_data = new EventBookingPlaceholdersData(
                        $notification_to_send->get_event(),
                        $notification_to_send->get_event_period(),
                        $booking,
                        $buy_tickets_per_booking[$booking->get_id()->get_value()],
                    );
                    $email_logs[] = $this->send_notification($notification_to_send->get_notification(), $placeholder_data);
                }
                $this->email_log_repo->bulk_add(new Collection($email_logs));

                $notification = $notification_to_send->get_notification();
                $scheduled_notification_log = ScheduledNotificationLogFactory::create([
                    'event_period_id' => $event_period_id,
                    'notification_id' => $notification->get_id()->get_value(),
                    'created' => DateTimeService::get_now_datetime_in_utc(),
                ]);
                $this->scheduled_notification_log_repo->add_by_entity($scheduled_notification_log);
            }
        }
    }
}

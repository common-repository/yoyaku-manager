<?php declare(strict_types=1);

namespace Yoyaku\Application\EventType\EventBooking;

use DateInterval;
use InvalidArgumentException;
use Yoyaku\Application\Common\AQuery;
use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Domain\DateTime\DateTimeService;
use Yoyaku\Domain\EventType\Event\EventFactory;
use Yoyaku\Domain\EventType\EventBooking\BookingStatus;
use Yoyaku\Domain\EventType\EventBooking\EventBookingFactory;
use Yoyaku\Domain\EventType\EventPeriod\EventPeriodFactory;
use Yoyaku\Infrastructure\Tables\Event\EventBookingsTable;
use Yoyaku\Infrastructure\Tables\Event\EventPeriodsTable;
use Yoyaku\Infrastructure\Tables\Event\EventsTable;

class EventBookingQuery extends AQuery
{
    /**
     * @param string $token
     * @throws InvalidArgumentException
     * @throws DataNotFoundException
     */
    public function get_placeholder_data_by_token($token)
    {
        global $wpdb;

        $bookings_table = EventBookingsTable::get_table_name();
        $events_table = EventsTable::get_table_name();
        $event_periods_table = EventPeriodsTable::get_table_name();

        $query = $wpdb->prepare(
            "SELECT
                e.id AS event_id,
                e.name AS event_name,
                e.use_approval_system AS event_use_approval_system,
                e.min_time_to_close_booking AS event_min_time_to_close_booking,
                e.min_time_to_cancel_booking AS event_min_time_to_cancel_booking,
                e.max_tickets_per_booking AS event_max_tickets_per_booking,
                e.is_online_payment AS event_is_online_payment,
                e.redirect_url AS event_redirect_url,

                ep.id AS ep_id,
                ep.event_id AS ep_event_id,
                ep.location AS ep_location,
                ep.start_datetime AS start_datetime,
                ep.end_datetime AS end_datetime,
                ep.max_ticket_count AS ep_max_ticket_count,
                ep.online_meeting_url AS ep_online_meeting_url,
                ep.zoom_meeting_id AS ep_zoom_meeting_id,
                ep.zoom_join_url AS ep_zoom_join_url,
                ep.zoom_start_url AS ep_zoom_start_url,
                ep.google_calendar_event_id AS ep_google_calendar_event_id,
                ep.google_meet_url AS ep_google_meet_url,

                eb.id AS id,
                eb.event_period_id AS event_period_id,
                eb.customer_id AS customer_id,
                eb.status AS status,
                eb.email AS email,
                eb.first_name AS first_name,
                eb.last_name AS last_name,
                eb.phone AS phone,
                
                eb.amount AS amount,
                eb.payment_status AS payment_status,
                eb.gateway AS gateway,
                eb.transaction_id AS transaction_id
            FROM $bookings_table eb
                INNER JOIN $event_periods_table ep ON ep.id = eb.event_period_id
                INNER JOIN $events_table e ON e.id = ep.event_id
            WHERE eb.token = %s",
            $token
        );

        $row = $wpdb->get_row($query, ARRAY_A);
        if (!$row) {
            throw new DataNotFoundException();
        }

        $event_data = [
            'id' => $row['event_id'],
            'name' => $row['event_name'],
            'use_approval_system' => $row['event_use_approval_system'],
            'min_time_to_close_booking' => $row['event_min_time_to_close_booking'],
            'min_time_to_cancel_booking' => $row['event_min_time_to_cancel_booking'],
            'max_tickets_per_booking' => $row['event_max_tickets_per_booking'],
            'is_online_payment' => $row['event_is_online_payment'],
            'redirect_url' => $row['event_redirect_url'],
        ];
        $event_period_data = [
            'id' => $row['ep_id'],
            'event_id' => $row['ep_event_id'],
            'location' => $row['ep_location'],
            'max_ticket_count' => $row['ep_max_ticket_count'],
            'start_datetime' => $row['start_datetime'],
            'end_datetime' => $row['end_datetime'],
            'online_meeting_url' => $row['ep_online_meeting_url'],
            'zoom_meeting_id' => $row['ep_zoom_meeting_id'],
            'zoom_join_url' => $row['ep_zoom_join_url'],
            'zoom_start_url' => $row['ep_zoom_start_url'],
            'google_calendar_event_id' => $row['ep_google_calendar_event_id'],
            'google_meet_url' => $row['ep_google_meet_url'],
        ];
        $booking_data = [
            'id' => $row['id'],
            'event_period_id' => $row['event_period_id'],
            'customer_id' => $row['customer_id'],
            'status' => $row['status'],
            'email' => $row['email'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'phone' => $row['phone'],
            'amount' => $row['amount'],
            'payment_status' => $row['payment_status'],
            'gateway' => $row['gateway'],
            'transaction_id' => $row['transaction_id'],
        ];

        $event = EventFactory::create($event_data);
        $event_period = EventPeriodFactory::create($event_period_data);
        $booking = EventBookingFactory::create($booking_data);
        return [$event, $event_period, $booking];
    }

    /**
     * 予約キャンセルブロックで必要なデータを取得
     * @param string $token EventBooking の token
     * @return array
     * @throws DataNotFoundException
     */
    public function get_cancel_data($token)
    {
        global $wpdb;

        $bookings_table = EventBookingsTable::get_table_name();
        $events_table = EventsTable::get_table_name();
        $periods_table = EventPeriodsTable::get_table_name();

        $query = $wpdb->prepare(
            "SELECT
                e.name AS event_name,
                e.min_time_to_cancel_booking AS min_time_to_cancel_booking,
                ep.start_datetime AS start_datetime,
                ep.end_datetime AS end_datetime,
                eb.status AS status,
                eb.token AS token
            FROM $bookings_table eb
                INNER JOIN $periods_table ep ON ep.id = eb.event_period_id
                INNER JOIN $events_table e ON e.id = ep.event_id
            WHERE eb.token = %s",
            $token
        );

        $row = $wpdb->get_row($query, ARRAY_A);
        if (!$row) {
            throw new DataNotFoundException();
        }

        $row['start_datetime'] = DateTimeService::get_custom_datetime_object_from_utc($row['start_datetime'])
            ->format(DATE_RFC3339);
        $row['end_datetime'] = DateTimeService::get_custom_datetime_object_from_utc($row['end_datetime'])
            ->format(DATE_RFC3339);

        $result = [
            'event_name' => $row['event_name'],
            'start_datetime' => $row['start_datetime'],
            'end_datetime' => $row['end_datetime'],
        ];

        // キャンセルできない場合は、error_messageのみのデータを返す
        $now = DateTimeService::get_now_datetime_object();
        $start_dt = DateTimeService::get_custom_datetime_object_from_utc($row['start_datetime']);
        $deadline_dt = $start_dt->sub(new DateInterval("PT{$row['min_time_to_cancel_booking']}M"));
        if ($deadline_dt < $now) {
            $result = ['error_message' => __('It is past cancellation due date.', 'yoyaku-manager')];
        } else if ($row['status'] === BookingStatus::CANCELED->value) {
            $result = ['error_message' => __('This booking has already been canceled.', 'yoyaku-manager')];
        } elseif ($row['status'] === BookingStatus::DISAPPROVED->value) {
            $result = ['error_message' => __('This booking has already been disapproved.', 'yoyaku-manager')];
        }

        return $result;
    }
}

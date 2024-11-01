<?php declare(strict_types=1);

namespace Yoyaku\Application\Notification;

use DateInterval;
use Yoyaku\Application\Common\AQuery;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\DateTime\DateTimeService;
use Yoyaku\Domain\EventType\Event\Event;
use Yoyaku\Domain\EventType\EventPeriod\EventPeriodFactory;
use Yoyaku\Domain\Notification\Notification;
use Yoyaku\Domain\Notification\NotificationFactory;
use Yoyaku\Domain\Notification\NotificationTiming;
use Yoyaku\Domain\Notification\ScheduledNotificationToSend;
use Yoyaku\Domain\ValueObject\DateTime\Minutes;
use Yoyaku\Domain\ValueObject\Number\Count;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Infrastructure\Tables\Event\EventPeriodsTable;
use Yoyaku\Infrastructure\Tables\Event\EventsTable;
use Yoyaku\Infrastructure\Tables\Notification\NotificationsEventsTable;
use Yoyaku\Infrastructure\Tables\Notification\NotificationsTable;
use Yoyaku\Infrastructure\Tables\Worker\WPUsersTable;

class NotificationQuery extends AQuery
{
    /**
     * @param array $filter
     * @return Collection<Notification>
     * @throws WpDbException
     */
    public function filter_by_notification_event($filter)
    {
        global $wpdb;

        $notifications_table = NotificationsTable::get_table_name();
        $notifications_events_table = NotificationsEventsTable::get_table_name();

        $where = '';
        if (!empty($filter['event_id'])) {
            $where .= $wpdb->prepare(' AND ne.event_id = %d', $filter['event_id']);
        }

        if (!empty($filter['timing'])) {
            $where .= $wpdb->prepare(' AND n.timing = %s', $filter['timing']);
        }

        $rows = $wpdb->get_results(
            "SELECT
                n.id AS id,
                n.name AS name,
                n.subject AS subject,
                n.content AS content,
                n.timing AS timing,
                n.days AS days,
                n.time AS time,
                n.is_before AS is_before
            FROM $notifications_events_table ne
                INNER JOIN $notifications_table n ON ne.notification_id = n.id
            WHERE 1=1 {$where}
            ORDER BY n.name",
            ARRAY_A
        );

        usort($rows, [$this, 'cmp_by_timing']);
        return call_user_func([NotificationFactory::class, 'create_collection'], $rows);
    }

    private function cmp_by_timing($a, $b)
    {
        $timing_priority = array_flip(NotificationTiming::values());
        if ($a['timing'] === $b['timing']) {
            if ($a['name'] === $b['name']) {
                return 0;
            } else {
                return ($a['name'] < $b['name']) ? -1 : 1;
            }
        } else {
            return ($timing_priority[$a['timing']] < $timing_priority[$b['timing']]) ? -1 : 1;
        }
    }

    /**
     * 現在通知する必要がある、イベント期間毎の定期通知を全て取得。通知済のデータも含む。
     * @return Collection<ScheduledNotificationToSend>
     * @throws WpDbException
     */
    public function get_to_send_scheduled_notifications()
    {
        global $wpdb;

        $notifications_table = NotificationsTable::get_table_name();
        $events_table = EventsTable::get_table_name();
        $events_periods_table = EventPeriodsTable::get_table_name();
        $notifications_events_table = NotificationsEventsTable::get_table_name();
        $wp_users_table = WPUsersTable::get_table_name();

        // イベント期間と、定期通知のセットを取得
        $rows = $wpdb->get_results(
            "SELECT
                e.id AS event_id,
                e.name AS event_name,
                e.min_time_to_close_booking AS event_min_time_to_close_booking,
                e.min_time_to_cancel_booking AS event_min_time_to_cancel_booking,
                e.max_tickets_per_booking AS event_max_tickets_per_booking,
                e.is_online_payment AS event_is_online_payment,
                e.use_approval_system AS event_use_approval_system,
                e.description AS event_description,

                ep.id AS ep_id,
                ep.wp_id AS ep_wp_id,
                ep.location AS ep_location,
                ep.start_datetime AS ep_start_datetime,
                ep.end_datetime AS ep_end_datetime,
                ep.max_ticket_count AS ep_max_ticket_count,
                ep.online_meeting_url AS ep_online_meeting_url,
                ep.zoom_meeting_id AS ep_zoom_meeting_id,
                ep.zoom_join_url AS ep_zoom_join_url,
                ep.zoom_start_url AS ep_zoom_start_url,
                ep.google_calendar_event_id AS ep_google_calendar_event_id,
                ep.google_meet_url AS ep_google_meet_url,
                
                n.id AS id,
                n.name AS name,
                n.timing AS timing,
                n.subject AS subject,
                n.content AS content,
                n.days AS days,
                n.time AS time,
                n.is_before AS is_before,
                       
                wp_user.display_name AS wp_user_display_name,
                wp_user.user_email AS wp_user_user_email
            FROM $events_table e
                INNER JOIN $events_periods_table ep ON e.id = ep.event_id
                INNER JOIN $notifications_events_table ne ON e.id = ne.event_id
                INNER JOIN $notifications_table n ON ne.notification_id = n.id
                LEFT JOIN $wp_users_table wp_user ON ep.wp_id = wp_user.ID
            WHERE n.timing = 'scheduled'
            ORDER BY ep.id, n.id",
            ARRAY_A,
        );

        // 通知する必要があるデータを取得
        $result = [];
        $now = DateTimeService::get_now_datetime_object();
        foreach ($rows as $row) {

            $days = new DateInterval("P{$row['days']}D");
            if ($row['is_before']) {
                $start_dt = DateTimeService::get_custom_datetime_object($row['ep_start_datetime']);
                $notify_dt_string = $start_dt->sub($days)->format('Y-m-d') . $row['time'];
                $notify_dt = DateTimeService::get_custom_datetime_object($notify_dt_string);
            } else {
                $end_dt = DateTimeService::get_custom_datetime_object($row['ep_end_datetime']);
                $notify_dt_string = $end_dt->add($days)->format('Y-m-d') . $row['time'];
                $notify_dt = DateTimeService::get_custom_datetime_object($notify_dt_string);
            }

            // サーバー停止などで通知できない場合を考慮して、通知日時+6時間後まで対応する
            $last_dt = $notify_dt->add(new DateInterval("PT6H"));

            if ($notify_dt <= $now && $now < $last_dt) {
                $notification = NotificationFactory::create($row);
                $event = new Event(
                    new Name($row['event_name']),
                    boolval($row['event_use_approval_system']),
                    new Minutes($row['event_min_time_to_close_booking']),
                    new Minutes($row['event_min_time_to_cancel_booking']),
                    new Count($row['event_max_tickets_per_booking']),
                    boolval($row['event_is_online_payment'])
                );
                $event_period = EventPeriodFactory::create([
                    'id' => $row['ep_id'],
                    'event_id' => $row['event_id'],
                    'wp_id' => $row['ep_wp_id'],
                    'location' => $row['ep_location'],
                    'start_datetime' => $row['ep_start_datetime'],
                    'end_datetime' => $row['ep_end_datetime'],
                    'max_ticket_count' => $row['ep_max_ticket_count'],
                    'online_meeting_url' => $row['ep_online_meeting_url'],
                    'zoom_meeting_id' => $row['ep_zoom_meeting_id'],
                    'zoom_join_url' => $row['ep_zoom_join_url'],
                    'zoom_start_url' => $row['ep_zoom_start_url'],
                    'google_calendar_event_id' => $row['ep_google_calendar_event_id'],
                    'google_meet_url' => $row['ep_google_meet_url'],

                    'wp_user_user_email' => $row['wp_user_user_email'],
                    'wp_user_display_name' => $row['wp_user_display_name'],
                ]);

                $result[] = new ScheduledNotificationToSend($notification, $event, $event_period);
            }
        }

        return new Collection($result);
    }
}

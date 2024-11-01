<?php declare(strict_types=1);

namespace Yoyaku\Application\EventType\Event;

use InvalidArgumentException;
use Yoyaku\Application\Common\AQuery;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\DateTime\DateTimeService;
use Yoyaku\Domain\EventType\EventPeriod\EventPeriodFactory;
use Yoyaku\Domain\ValueObject\Number\Count;
use Yoyaku\Infrastructure\Tables\Event\EventBookingsTable;
use Yoyaku\Infrastructure\Tables\Event\EventPeriodsTable;
use Yoyaku\Infrastructure\Tables\Event\EventsTable;
use Yoyaku\Infrastructure\Tables\Event\EventTicketBookingsTable;
use Yoyaku\Infrastructure\Tables\Event\EventTicketsTable;
use Yoyaku\Infrastructure\Tables\Worker\WPUsersTable;

class EventQuery extends AQuery
{
    private string $events_table;
    private string $periods_table;
    private string $wp_users_table;

    public function __construct()
    {
        parent::__construct();
        $this->events_table = EventsTable::get_table_name();
        $this->periods_table = EventPeriodsTable::get_table_name();
        $this->wp_users_table = WPUsersTable::get_table_name();
    }

    /**
     * フロント用 顧客が予約可能なイベント期間を取得する（開催日時の昇順）
     * @param int $event_id
     * @return Collection
     * @throws WpDbException
     * @throws InvalidArgumentException
     */
    public function filter_event_periods_for_front($event_id)
    {
        global $wpdb;

        $event_tickets_table = EventTicketsTable::get_table_name();
        $event_bookings_table = EventBookingsTable::get_table_name();
        $ticket_booking_table = EventTicketBookingsTable::get_table_name();
        $where = $wpdb->prepare(" AND e.id = %d", $event_id);
        // 予約締め切り前の条件を追加
        $now = DateTimeService::get_now_datetime_in_utc();
        $where .= " AND STR_TO_DATE('{$now}', '%Y-%m-%d %H:%i:%s') <= ep.start_datetime - interval e.min_time_to_close_booking minute";

        $period_rows = $wpdb->get_results(
            "SELECT
                ep.id AS id,
                ep.uuid AS uuid,
                ep.event_id AS event_id,
                ep.wp_id AS wp_id,
                ep.location AS location,
                ep.max_ticket_count AS max_ticket_count,
                ep.start_datetime AS start_datetime,
                ep.end_datetime AS end_datetime,
                ep.online_meeting_url AS online_meeting_url,
                ep.zoom_meeting_id AS zoom_meeting_id,
                ep.zoom_join_url AS zoom_join_url,
                ep.zoom_start_url AS zoom_start_url,
                ep.google_calendar_event_id AS google_calendar_event_id,
                ep.google_meet_url AS google_meet_url,
    
                wp_user.ID AS wp_user_id, 
                wp_user.display_name AS wp_user_display_name,
                wp_user.user_email AS wp_user_user_email
            FROM $this->events_table e
                LEFT JOIN {$this->periods_table} ep ON e.id = ep.event_id
                LEFT JOIN {$this->wp_users_table} wp_user ON ep.wp_id = wp_user.ID
            WHERE 1=1 {$where}
            ORDER BY ep.start_datetime",
            ARRAY_A
        );

        if (!$period_rows) {
            return new Collection();
        }

        $result = new Collection();
        $period_ids = [];
        foreach ($period_rows as $period_row) {
            $period = EventPeriodFactory::create($period_row);
            $period->set_sold_ticket_count(new Count(0));
            $result->add_item($period, intval($period_row['id']));
            $period_ids[] = intval($period_row['id']);
        }

        $where = "";
        if ($period_ids) {
            $where .= ' AND ep.id IN (' . implode(',', wp_parse_id_list($period_ids)) . ')';
        }

        $buy_count_rows = $wpdb->get_results(
            "SELECT
                ep.id AS id,
                SUM(etb.buy_count) AS sold_ticket_count
            FROM $this->events_table e
                LEFT JOIN $this->periods_table ep ON e.id = ep.event_id
                LEFT JOIN $event_tickets_table et ON e.id = et.event_id
                LEFT JOIN $event_bookings_table eb ON ep.id = eb.event_period_id
                LEFT JOIN $ticket_booking_table etb ON eb.id = etb.event_booking_id AND et.id = etb.event_ticket_id
            WHERE eb.status IN ('approved', 'pending')
                {$where}
            GROUP BY ep.id",
            ARRAY_A,
        );

        foreach ($buy_count_rows as $buy_count_row) {
            $id = intval($buy_count_row['id']);
            if ($result->key_exists($id)) {
                $result->get_item($id)->set_sold_ticket_count(
                    new Count($buy_count_row['sold_ticket_count'])
                );
            }
        }

        return $result;
    }
}

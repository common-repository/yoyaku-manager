<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Repository\EventType;

use InvalidArgumentException;
use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\EventType\EventPeriod\EventPeriodFactory;
use Yoyaku\Infrastructure\Repository\ARepository;
use Yoyaku\Infrastructure\Tables\Event\EventPeriodsTable;
use Yoyaku\Infrastructure\Tables\Worker\WPUsersTable;

/**
 * Class EventPeriodsRepository
 */
class EventPeriodRepository extends ARepository
{

    const FACTORY = EventPeriodFactory::class;
    private string $wp_users_table;

    public function __construct()
    {
        $table = EventPeriodsTable::get_table_name();
        $this->wp_users_table = WPUsersTable::get_table_name();
        parent::__construct($table);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws DataNotFoundException
     */
    public function get_by_id($id)
    {
        global $wpdb;

        $query = $wpdb->prepare("
            SELECT
                ep.id AS id,
                ep.uuid AS uuid,
                ep.event_id AS event_id,
                ep.wp_id AS wp_id,
                ep.location AS location,
                ep.event_id AS event_id,
                ep.start_datetime AS start_datetime,
                ep.end_datetime AS end_datetime,
                ep.max_ticket_count AS max_ticket_count,
                ep.online_meeting_url AS online_meeting_url,
                ep.zoom_meeting_id AS zoom_meeting_id,
                ep.zoom_join_url AS zoom_join_url,
                ep.zoom_start_url AS zoom_start_url,
                ep.google_calendar_event_id AS google_calendar_event_id,
                ep.google_meet_url AS google_meet_url,
                
                wp_user.ID AS wp_user_id, 
                wp_user.display_name AS wp_user_display_name,
                wp_user.user_email AS wp_user_user_email
            FROM {$this->table} ep
                LEFT JOIN {$this->wp_users_table} wp_user ON ep.wp_id = wp_user.ID
            WHERE ep.id = %d",
            $id,
        );
        $row = $wpdb->get_row($query, ARRAY_A);
        if (!$row) {
            throw new DataNotFoundException();
        }

        return call_user_func([static::FACTORY, 'create'], $row);
    }

    /**
     * @param string $uuid
     * @return mixed
     * @throws DataNotFoundException
     */
    public function get_by_uuid($uuid)
    {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT
                ep.id AS id,
                ep.uuid AS uuid,
                ep.event_id AS event_id,
                ep.wp_id AS wp_id,
                ep.location AS location,
                ep.event_id AS event_id,
                ep.start_datetime AS start_datetime,
                ep.end_datetime AS end_datetime,
                ep.max_ticket_count AS max_ticket_count,
                ep.online_meeting_url AS online_meeting_url,
                ep.zoom_meeting_id AS zoom_meeting_id,
                ep.zoom_join_url AS zoom_join_url,
                ep.zoom_start_url AS zoom_start_url,
                ep.google_calendar_event_id AS google_calendar_event_id,
                ep.google_meet_url AS google_meet_url,
                
                wp_user.ID AS wp_user_id, 
                wp_user.display_name AS wp_user_display_name,
                wp_user.user_email AS wp_user_user_email
            FROM {$this->table} ep
                LEFT JOIN {$this->wp_users_table} wp_user ON ep.wp_id = wp_user.ID
            WHERE ep.uuid = %s",
            $uuid,
        );
        $row = $wpdb->get_row($query, ARRAY_A);
        if (!$row) {
            throw new DataNotFoundException();
        }

        return call_user_func([static::FACTORY, 'create'], $row);
    }

    /**
     * @param array $filter
     * @param bool $count
     * @return Collection|int
     * @throws WpDbException
     * @throws InvalidArgumentException
     */
    public function filter($filter, $count = false)
    {
        global $wpdb;

        $where = $wpdb->prepare(' AND ep.event_id = %d', $filter["event_id"]);

        if (isset($filter['start_datetime'])) {
            $where .= $wpdb->prepare(' AND ep.start_datetime >= %s', $filter['start_datetime']);
        }

        $order = "ORDER BY start_datetime ASC, id ASC";

        $limit = '';
        if (isset($filter['page'], $filter['per_page'])) {
            $limit = $this->get_limit(absint($filter['page']), absint($filter['per_page']));
        }

        if ($count) {
            return intval($wpdb->get_var(
                "SELECT COUNT(*) 
                FROM {$this->table} ep
                    LEFT JOIN {$this->wp_users_table} wp_user ON ep.wp_id = wp_user.ID
                WHERE 1=1 {$where}",
            ));
        } else {
            $rows = $wpdb->get_results(
                "SELECT 
                    ep.id AS id,
                    ep.uuid AS uuid,
                    ep.event_id AS event_id,
                    ep.wp_id AS wp_id,
                    ep.event_id AS event_id,
                    ep.location AS location,
                    ep.start_datetime AS start_datetime,
                    ep.end_datetime AS end_datetime,
                    ep.max_ticket_count AS max_ticket_count,
                    ep.online_meeting_url AS online_meeting_url,
                    ep.zoom_meeting_id AS zoom_meeting_id,
                    ep.zoom_join_url AS zoom_join_url,
                    ep.zoom_start_url AS zoom_start_url,
                    ep.google_calendar_event_id AS google_calendar_event_id,
                    ep.google_meet_url AS google_meet_url,
                    
                    wp_user.ID AS wp_user_id, 
                    wp_user.display_name AS wp_user_display_name,
                    wp_user.user_email AS wp_user_user_email
                FROM $this->table ep
                    LEFT JOIN $this->wp_users_table wp_user ON ep.wp_id = wp_user.ID
                WHERE 1=1 {$where}
                {$order}
                $limit",
                ARRAY_A,
            );
            return call_user_func([static::FACTORY, 'create_collection'], $rows);
        }
    }
}

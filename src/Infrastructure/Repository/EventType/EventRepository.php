<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Repository\EventType;

use InvalidArgumentException;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\DateTime\DateTimeService;
use Yoyaku\Domain\EventType\Event\EventFactory;
use Yoyaku\Infrastructure\Repository\ARepository;
use Yoyaku\Infrastructure\Tables\Event\EventPeriodsTable;
use Yoyaku\Infrastructure\Tables\Event\EventsTable;
use Yoyaku\Infrastructure\Tables\Event\EventTicketsTable;

class EventRepository extends ARepository
{
    const FACTORY = EventFactory::class;
    private string $event_tickets_table;
    private string $event_periods_table;

    public function __construct()
    {
        $table = EventsTable::get_table_name();
        $this->event_tickets_table = EventTicketsTable::get_table_name();
        $this->event_periods_table = EventPeriodsTable::get_table_name();

        parent::__construct($table);
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

        $where = '';

        if (isset($filter['search'])) {
            $where .= $this->_get_search_sql($filter['search'], ['e.name']);
        }

        $orderby = "ORDER BY id DESC";
        if (!empty($filter['orderby'])) {
            $order_column = $filter['orderby'];
            $order = '';
            if (!empty($filter['order'])) {
                $order = $this->get_order($filter['order']);
            }
            $orderby = "ORDER BY $order_column $order";
        }

        $limit = '';
        if (isset($filter['page'], $filter['per_page'])) {
            $limit = $this->get_limit(absint($filter['page']), absint($filter['per_page']));
        }

        if ($count) {
            return intval($wpdb->get_var(
                "SELECT COUNT(*) 
                FROM $this->table e 
                    LEFT JOIN $this->event_tickets_table et ON e.id = et.event_id
                WHERE 1=1 $where"
            ));
        } else {
            $rows = $wpdb->get_results(
                "SELECT
                    e.id AS id,
                    e.name AS name,
                    e.min_time_to_close_booking AS min_time_to_close_booking,
                    e.min_time_to_cancel_booking AS min_time_to_cancel_booking,
                    e.max_tickets_per_booking AS max_tickets_per_booking,
                    e.is_online_payment AS is_online_payment,
                    e.description AS description,
                    e.use_approval_system AS use_approval_system,
                    e.show_worker AS show_worker,
                    e.redirect_url AS redirect_url,
                    
                    et.id AS event_ticket_id,
                    et.event_id AS event_ticket_event_id,
                    et.name AS event_ticket_name,
                    et.price AS event_ticket_price,
                    et.ticket_count AS event_ticket_ticket_count
                FROM $this->table e
                    LEFT JOIN $this->event_tickets_table et ON e.id = et.event_id
                WHERE 1=1 {$where}
                {$orderby}
                $limit",
                ARRAY_A
            );
            return call_user_func([static::FACTORY, 'create_collection'], $rows);
        }
    }

    /**
     * @param $filter
     * @return array
     * @throws WpDbException
     */
    public function filter_for_calendar($filter)
    {
        global $wpdb;

        $where = '';

        if (isset($filter['date_from'])) {
            $dt = DateTimeService::get_custom_datetime_in_utc($filter['date_from'] . ' 00:00:00');
            $where .= $wpdb->prepare(' AND ep.start_datetime >= %s', $dt);
        }

        if (isset($filter['date_to'])) {
            $dt = DateTimeService::get_custom_datetime_in_utc($filter['date_to'] . ' 23:59:59');
            $where .= $wpdb->prepare(' AND %s >= ep.start_datetime ', $dt);
        }

        $rows = $wpdb->get_results(
            "SELECT
                e.id AS id,
                e.name AS name,
 
                ep.start_datetime AS start_datetime,
                ep.end_datetime AS end_datetime
            FROM $this->table e
                INNER JOIN $this->event_periods_table ep ON e.id = ep.event_id
            WHERE 1=1 $where",
            ARRAY_A
        );

        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'start_datetime' => DateTimeService::get_custom_datetime_object_from_utc($row['start_datetime'])->format(DATE_RFC3339),
                'end_datetime' => DateTimeService::get_custom_datetime_object_from_utc($row['end_datetime'])->format(DATE_RFC3339),
            ];
        }
        return $result;
    }

}

<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Repository\EventType;

use InvalidArgumentException;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\EventType\EventTicket\EventTicketFactory;
use Yoyaku\Infrastructure\Repository\ARepository;
use Yoyaku\Infrastructure\Tables\Event\EventBookingsTable;
use Yoyaku\Infrastructure\Tables\Event\EventPeriodsTable;
use Yoyaku\Infrastructure\Tables\Event\EventTicketBookingsTable;
use Yoyaku\Infrastructure\Tables\Event\EventTicketsTable;

/**
 * Class EventTicketRepository
 */
class EventTicketRepository extends ARepository
{
    const FACTORY = EventTicketFactory::class;

    public function __construct()
    {
        $table = EventTicketsTable::get_table_name();
        parent::__construct($table);
    }

    /**
     * @param array $filter
     * @return Collection
     * @throws WpDbException
     * @throws InvalidArgumentException
     */
    public function filter($filter)
    {
        global $wpdb;

        $event_periods_table = EventPeriodsTable::get_table_name();
        $bookings_table = EventBookingsTable::get_table_name();
        $ticket_bookings_table = EventTicketBookingsTable::get_table_name();

        $where = '';
        if (isset($filter['event_id'])) {
            $where .= $wpdb->prepare(' AND et.event_id = %d', $filter['event_id']);
        }

        if (!empty($filter['event_period_uuid'])) {
            $where = $wpdb->prepare(' AND ep.uuid = %s', $filter['event_period_uuid']);
        }

        if (!empty($filter['event_period_id'])) {
            $where = $wpdb->prepare(' AND ep.id = %d', $filter['event_period_id']);
        }

        if (!empty($filter['event_booking_id'])) {
            $where = $wpdb->prepare(' AND eb.id = %d', $filter['event_booking_id']);
        }

        if (!empty($filter['id__in'])) {
            $where .= ' AND et.id IN (' . implode(',', wp_parse_id_list($filter['id__in'])) . ')';
        }

        $tickets = $wpdb->get_results(
            "SELECT DISTINCT
                et.id AS id,
                et.event_id AS event_id,
                et.name AS name,
                et.price AS price,
                et.ticket_count AS ticket_count
            FROM $this->table et
                LEFT JOIN $event_periods_table ep ON et.event_id = ep.event_id
                LEFT JOIN $bookings_table eb ON ep.id = eb.event_period_id
            WHERE 1=1 $where",
            ARRAY_A
        );

        if (!isset($filter['with_sold_count']) || !$filter['with_sold_count']) {
            return call_user_func([static::FACTORY, 'create_collection'], $tickets);
        }

        $sold_tickets = $wpdb->get_results(
            "SELECT
                et.id AS id,
                et.name AS name,
                SUM(etb.buy_count) AS sold_ticket_count
            FROM {$this->table} et
                LEFT JOIN {$event_periods_table} ep ON et.event_id = ep.event_id
                LEFT JOIN {$bookings_table} eb ON ep.id = eb.event_period_id
                LEFT JOIN {$ticket_bookings_table} etb ON eb.id = etb.event_booking_id AND et.id = etb.event_ticket_id
                    AND eb.id = etb.event_booking_id
            WHERE eb.status IN ('approved', 'pending')
                {$where}
            GROUP BY et.id
            ",
            ARRAY_A
        );

        foreach ($tickets as $index => $ticket) {
            $tickets[$index]['sold_ticket_count'] = 0;
            foreach ($sold_tickets as $soldTicket) {
                if ($ticket['id'] === $soldTicket['id']) {
                    $tickets[$index]['sold_ticket_count'] = intval($soldTicket['sold_ticket_count']);
                    break;
                }
            }
        }
        return call_user_func([static::FACTORY, 'create_collection'], $tickets);
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Repository\EventType;

use InvalidArgumentException;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\EventType\EventTicket\BuyEventTicketFactory;
use Yoyaku\Domain\EventType\EventTicketBooking\EventTicketBooking;
use Yoyaku\Domain\EventType\EventTicketBooking\EventTicketBookingFactory;
use Yoyaku\Infrastructure\Repository\ARepository;
use Yoyaku\Infrastructure\Tables\Event\EventBookingsTable;
use Yoyaku\Infrastructure\Tables\Event\EventTicketBookingsTable;
use Yoyaku\Infrastructure\Tables\Event\EventTicketsTable;

class EventTicketBookingRepository extends ARepository
{
    const FACTORY = EventTicketBookingFactory::class;

    public function __construct()
    {
        $table = EventTicketBookingsTable::get_table_name();
        parent::__construct($table);
    }

    /**
     * @param int $event_booking_id
     * @return Collection
     * @throws WpDbException
     * @throws InvalidArgumentException
     */
    public function filter_by_event_booking_id($event_booking_id)
    {
        global $wpdb;

        $event_tickets_table = EventTicketsTable::get_table_name();
        $where = $wpdb->prepare(' AND etb.event_booking_id = %d', $event_booking_id);

        $rows = $wpdb->get_results(
            "SELECT 
                et.id AS id,
                et.name AS name,
                et.price AS price,
                etb.buy_count AS buy_count
            FROM $this->table etb
                LEFT JOIN $event_tickets_table et ON etb.event_ticket_id = et.id
            WHERE 1=1 $where",
            ARRAY_A,
        );

        return BuyEventTicketFactory::create_collection($rows);
    }

    /**
     * @param array<int> $event_booking_ids
     * @return array
     * @throws WpDbException
     * @throws InvalidArgumentException
     */
    public function filter_by_event_booking_ids($event_booking_ids)
    {
        global $wpdb;

        $event_tickets_table = EventTicketsTable::get_table_name();
        $where = ' AND etb.event_booking_id IN (' . implode(', ', wp_parse_id_list($event_booking_ids)) . ')';

        $rows = $wpdb->get_results(
            "SELECT 
                et.id AS id,
                et.name AS name,
                et.price AS price,
                etb.event_booking_id AS event_booking_id,
                etb.buy_count AS buy_count
            FROM $this->table etb
                LEFT JOIN $event_tickets_table et ON etb.event_ticket_id = et.id
            WHERE 1=1 $where",
            ARRAY_A,
        );

        $result = [];
        foreach ($rows as $row) {
            $event_booking_id = intval($row['event_booking_id']);
            if (!isset($result[$row['event_booking_id']])) {
                $result[$event_booking_id] = new Collection();
            }
            $result[$event_booking_id]->add_item(
                BuyEventTicketFactory::create([
                    'id' => intval($row['id']),
                    'buy_count' => intval($row['buy_count']),
                    'name' => $row['name'],
                    'price' => $row['name'],
                ]),
                $event_booking_id,
            );
        }
        return $result;
    }

    /**
     * @param int $period_id
     * @param array $ticket_ids
     * @return array
     * @throws WpDbException
     */
    public function get_sold_ticket_count_list($period_id, $ticket_ids)
    {
        global $wpdb;

        if (!$ticket_ids) {
            return [];
        }

        $event_bookings_table = EventBookingsTable::get_table_name();
        $where = ' AND etb.event_ticket_id IN (' . implode(', ', wp_parse_id_list($ticket_ids)) . ')';
        $where .= $wpdb->prepare(' AND eb.event_period_id = %d', $period_id);

        $rows = $wpdb->get_results(
            "SELECT
                etb.event_ticket_id,
                SUM(buy_count) AS event_sold_ticket
            FROM $this->table etb
                LEFT JOIN $event_bookings_table eb ON etb.event_booking_id = eb.id
            WHERE eb.status IN ('approved', 'pending')
                {$where}
            GROUP BY etb.event_ticket_id",
            ARRAY_A,
        );

        $ticket_id_sold_ticket_map = array_column($rows, 'event_sold_ticket', 'event_ticket_id');

        $result = [];
        foreach ($ticket_ids as $id) {
            $result[$id] = 0;
            if (isset($ticket_id_sold_ticket_map[$id])) {
                $result[$id] = intval($ticket_id_sold_ticket_map[$id]);
            }
        }

        return $result;
    }

    /**
     * @param $ticket_bookings Collection<EventTicketBooking>
     * @return bool|int 追加した件数. 失敗した場合はfalse
     */
    public function bulk_add($ticket_bookings)
    {
        global $wpdb;

        if (!$ticket_bookings->length()) {
            return 0;
        }

        $query = " INSERT INTO {$this->table} (`event_booking_id`, `event_ticket_id`, `buy_count`) VALUES ";
        $insert_values = [];
        /** @var EventTicketBooking $ticket_booking */
        foreach ($ticket_bookings->get_items() as $ticket_booking) {
            $insert_values[] = $wpdb->prepare("(%d, %d, %d)",
                $ticket_booking->get_event_booking_id()->get_value(),
                $ticket_booking->get_event_ticket_id()->get_value(),
                $ticket_booking->get_buy_count()->get_value(),
            );
        }
        $query .= implode(',', $insert_values);
        return $wpdb->query($query);
    }
}

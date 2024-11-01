<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Tables\Event;


use mysqli_result;
use Yoyaku\Infrastructure\Tables\ATable;

/**
 * EventBookingsとEventTicketsの中間テーブル
 */
class EventTicketBookingsTable extends ATable
{
    const TABLE = 'event_ticket_bookings';

    /**
     * @return bool|int|mysqli_result|null
     */
    public static function build_table()
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $event_bookings_table = EventBookingsTable::get_table_name();
        $event_tickets_table = EventTicketsTable::get_table_name();

        return $wpdb->query(
            $wpdb->prepare("
                CREATE TABLE IF NOT EXISTS $table_name (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `event_booking_id` bigint(20) unsigned NOT NULL,
                    `event_ticket_id` bigint(20) unsigned NULL,
                    `buy_count` int(11) NOT NULL,
                    PRIMARY KEY (`id`),
                    CONSTRAINT %i FOREIGN KEY (`event_booking_id`) REFERENCES %i (`id`) ON DELETE CASCADE,
                    CONSTRAINT %i FOREIGN KEY (`event_ticket_id`) REFERENCES %i (`id`) ON DELETE CASCADE
                )
                DEFAULT CHARSET=utf8 COLLATE utf8_general_ci",
                ["{$table_name}_fk_1", $event_bookings_table, "{$table_name}_fk_2", $event_tickets_table],
            )
        );
    }
}

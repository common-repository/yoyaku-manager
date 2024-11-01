<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Tables\Event;

use mysqli_result;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Infrastructure\Tables\ATable;

/**
 * イベントチケットテーブル
 */
class EventTicketsTable extends ATable
{
    const TABLE = 'event_tickets';

    /**
     * @return bool|int|mysqli_result|null
     */
    public static function build_table()
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $events_table = EventsTable::get_table_name();
        $name = Name::MAX_LENGTH;

        return $wpdb->query(
            $wpdb->prepare("
                CREATE TABLE IF NOT EXISTS $table_name (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `event_id` bigint(20) unsigned NOT NULL,
                    `name` varchar(%d) NOT NULL,
                    `price` double DEFAULT 0,
                    `ticket_count` INT(11) NOT NULL,
                    PRIMARY KEY (`id`),
                    CONSTRAINT %i FOREIGN KEY (`event_id`) REFERENCES %i (`id`) ON DELETE CASCADE
                )
                DEFAULT CHARSET=utf8 COLLATE utf8_general_ci",
                [$name, "{$table_name}_fk_1", $events_table]
            )
        );
    }
}

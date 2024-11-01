<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Tables\Notification;

use mysqli_result;
use Yoyaku\Infrastructure\Tables\ATable;
use Yoyaku\Infrastructure\Tables\Event\EventsTable;

class NotificationsEventsTable extends ATable
{
    const TABLE = 'notifications_events';

    /**
     * @return bool|int|mysqli_result|null
     */
    public static function build_table()
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $events_table = EventsTable::get_table_name();
        $notifications_table = NotificationsTable::get_table_name();

        return $wpdb->query(
            $wpdb->prepare("
                CREATE TABLE IF NOT EXISTS $table_name (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `event_id` bigint(20) unsigned NOT NULL,
                    `notification_id` bigint(20) unsigned NOT NULL,
                    PRIMARY KEY (`id`),
                    CONSTRAINT %i FOREIGN KEY (`event_id`) REFERENCES %i (`id`) ON DELETE CASCADE,
                    CONSTRAINT %i FOREIGN KEY (`notification_id`) REFERENCES %i (`id`) ON DELETE CASCADE
                )
                DEFAULT CHARSET=utf8 COLLATE utf8_general_ci",
                ["{$table_name}_fk_1", $events_table, "{$table_name}_fk_2", $notifications_table]
            )
        );
    }
}

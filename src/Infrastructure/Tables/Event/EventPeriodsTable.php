<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Tables\Event;

use mysqli_result;
use Yoyaku\Domain\ValueObject\String\Address;
use Yoyaku\Domain\ValueObject\String\Url;
use Yoyaku\Infrastructure\Tables\ATable;
use Yoyaku\Infrastructure\Tables\Worker\WPUsersTable;

/**
 * イベント期間
 */
class EventPeriodsTable extends ATable
{
    const TABLE = 'events_periods';

    /**
     * @return bool|int|mysqli_result|null
     */
    public static function build_table()
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $events_table = EventsTable::get_table_name();
        $wp_users_table = WPUsersTable::get_table_name();
        $url = Url::MAX_LENGTH;
        $address = Address::MAX_LENGTH;

        return $wpdb->query(
            $wpdb->prepare("
                CREATE TABLE IF NOT EXISTS $table_name (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `uuid` CHAR(36) NOT NULL,
                    `event_id` bigint(20) unsigned NOT NULL,
                    `wp_id` bigint(20) unsigned NULL,
                    `start_datetime` DATETIME NOT NULL,
                    `end_datetime` DATETIME NOT NULL,
                    `max_ticket_count` INT(11) NOT NULL,
                    `location` varchar(%d) NOT NULL default '',
                    `online_meeting_url` varchar(%d) NOT NULL DEFAULT '',
                    `zoom_meeting_id` bigint(20) unsigned NULL,
                    `zoom_start_url` varchar(%d) NOT NULL DEFAULT '',
                    `zoom_join_url` varchar(%d) NOT NULL DEFAULT '',
                    `google_calendar_event_id` VARCHAR(255) NOT NULL DEFAULT '',
                    `google_meet_url` VARCHAR(255) NOT NULL DEFAULT '',
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `uuid` (`uuid`),
                    CONSTRAINT %i FOREIGN KEY (`event_id`) REFERENCES %i (`id`) ON DELETE CASCADE,
                    CONSTRAINT %i FOREIGN KEY (`wp_id`) REFERENCES %i (`ID`) ON DELETE SET NULL
                )
                DEFAULT CHARSET=utf8 COLLATE utf8_general_ci",
                [$address, $url, $url, $url, "{$table_name}_fk_1", $events_table, "{$table_name}_fk_2",
                    $wp_users_table],
            )
        );
    }
}

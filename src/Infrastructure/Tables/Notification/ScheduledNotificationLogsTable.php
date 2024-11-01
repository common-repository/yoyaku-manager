<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Tables\Notification;

use mysqli_result;
use Yoyaku\Infrastructure\Tables\ATable;
use Yoyaku\Infrastructure\Tables\Event\EventPeriodsTable;

/**
 * 定期通知送信履歴のテーブル
 */
class ScheduledNotificationLogsTable extends ATable
{
    const TABLE = 'scheduled_notification_log';

    /**
     * @return bool|int|mysqli_result|null
     */
    public static function build_table()
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $notification_table = NotificationsTable::get_table_name();
        $event_periods_table = EventPeriodsTable::get_table_name();

        return $wpdb->query(
            $wpdb->prepare("
            CREATE TABLE IF NOT EXISTS $table_name (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `notification_id` bigint(20) unsigned NOT NULL,
                `event_period_id` bigint(20) unsigned NOT NULL,
                `created` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `notification_id_event_period_id` (`notification_id` ,`event_period_id`),
                CONSTRAINT %i FOREIGN KEY (`notification_id`) REFERENCES %i (`id`) ON DELETE CASCADE,
                CONSTRAINT %i FOREIGN KEY (`event_period_id`) REFERENCES %i (`id`) ON DELETE CASCADE
            )
            DEFAULT CHARSET=utf8 COLLATE utf8_general_ci",
                ["{$table_name}_fk_1", $notification_table, "{$table_name}_fk_2", $event_periods_table],
            )
        );
    }
}

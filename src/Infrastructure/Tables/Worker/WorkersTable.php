<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Tables\Worker;

use mysqli_result;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Infrastructure\Tables\ATable;

class WorkersTable extends ATable
{
    const TABLE = 'workers';

    /**
     * @return bool|int|mysqli_result|null
     */
    public static function build_table()
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $wp_users_table = WPUsersTable::get_table_name();
        $name = Name::MAX_LENGTH;

        // idはwpユーザーのidと同じにするため、AUTO_INCREMENTにしない。
        return $wpdb->query(
            $wpdb->prepare("
                CREATE TABLE IF NOT EXISTS $table_name  (
                    `id` bigint(20) unsigned NOT NULL,
                    `zoom_user_id` varchar(%d) NOT NULL DEFAULT '',
                    `google_calendar_token` TEXT NOT NULL DEFAULT '',
                    `google_calendar_id` TEXT NOT NULL DEFAULT '',
                    PRIMARY KEY (`id`),
                    CONSTRAINT %i FOREIGN KEY (`id`) REFERENCES %i (`ID`) ON DELETE CASCADE
                )
                DEFAULT CHARSET=utf8 COLLATE utf8_general_ci",
                [$name, "{$table_name}_fk_1", $wp_users_table]
            )
        );
    }
}

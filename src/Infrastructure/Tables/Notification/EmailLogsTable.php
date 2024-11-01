<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Tables\Notification;

use mysqli_result;
use Yoyaku\Domain\ValueObject\String\Email;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Infrastructure\Tables\ATable;
use Yoyaku\Infrastructure\Tables\Customer\CustomersTable;

/**
 * 即時通知履歴のテーブル
 */
class EmailLogsTable extends ATable
{
    const TABLE = 'email_log';

    /**
     * @return bool|int|mysqli_result|null
     */
    public static function build_table()
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $users_table = CustomersTable::get_table_name();
        $email = Email::MAX_LENGTH;
        $name = Name::MAX_LENGTH;

        return $wpdb->query(
            $wpdb->prepare("
                CREATE TABLE IF NOT EXISTS $table_name (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `customer_id` bigint(20) unsigned NOT NULL,
                    `sent_datetime` DATETIME NOT NULL,
                    `sent` TINYINT(1) NOT NULL,
                    `to` varchar(%d) NOT NULL,
                    `subject` varchar(%d) NOT NULL DEFAULT '',
                    `content` TEXT NOT NULL DEFAULT '',
                    PRIMARY KEY (`id`),
                    CONSTRAINT %i FOREIGN KEY (`customer_id`) REFERENCES %i (`id`) ON DELETE CASCADE
                )
                DEFAULT CHARSET=utf8 COLLATE utf8_general_ci",
                [$email, $name, "{$table_name}_fk_1", $users_table]
            )
        );
    }
}

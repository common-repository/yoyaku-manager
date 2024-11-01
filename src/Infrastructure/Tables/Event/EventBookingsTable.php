<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Tables\Event;

use mysqli_result;
use Yoyaku\Domain\Customer\ZipCode;
use Yoyaku\Domain\ValueObject\String\Address;
use Yoyaku\Domain\ValueObject\String\Email;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Domain\ValueObject\String\Phone;
use Yoyaku\Infrastructure\Tables\ATable;
use Yoyaku\Infrastructure\Tables\Customer\CustomersTable;

/**
 * 顧客のイベント予約管理テーブル event_period と user の中間テーブル
 */
class EventBookingsTable extends ATable
{
    const TABLE = 'event_bookings';

    /**
     * @return bool|int|mysqli_result|null
     */
    public static function build_table()
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $event_periods_table = EventPeriodsTable::get_table_name();
        $customers_table = CustomersTable::get_table_name();
        $name = Name::MAX_LENGTH;
        $email = Email::MAX_LENGTH;
        $phone = Phone::MAX_LENGTH;
        $address = Address::MAX_LENGTH;
        $zipcode = ZipCode::MAX_LENGTH;

        return $wpdb->query(
            $wpdb->prepare("
            CREATE TABLE IF NOT EXISTS $table_name (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `customer_id` bigint(20) unsigned NOT NULL,
                `event_period_id` bigint(20) unsigned NOT NULL,
                `email` varchar(%d) NOT NULL,
                `first_name` varchar(%d) NOT NULL,
                `last_name` varchar(%d) NOT NULL,
                `first_name_ruby` varchar(%d) NOT NULL DEFAULT '',
                `last_name_ruby` varchar(%d) NOT NULL DEFAULT '',
                `phone` varchar(%d) NOT NULL DEFAULT '',
                `birthday` date NULL,
                `address` varchar(%d) NOT NULL DEFAULT '',
                `zipcode` varchar(%d) NOT NULL DEFAULT '',
                `gender` ENUM('male', 'female', '') NOT NULL DEFAULT '',
                `status` ENUM('approved', 'pending', 'canceled', 'disapproved') NOT NULL,
                `memo` TEXT NOT NULL DEFAULT '',
                `amount` DOUBLE NOT NULL DEFAULT 0,
                `payment_status` ENUM('paid', 'pending', 'refunded') NOT NULL,
                `gateway` ENUM('on_site', 'paypal', 'stripe') NOT NULL,
                `transaction_id` varchar(255) NULL,
                `token` CHAR(100) NOT NULL,
                `created` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `token` (`token`),
                UNIQUE KEY `customer_id_event_period_id` (`customer_id` ,`event_period_id`),
                CONSTRAINT %i FOREIGN KEY (`event_period_id`) REFERENCES %i (`id`) ON DELETE CASCADE,
                CONSTRAINT %i FOREIGN KEY (`customer_id`) REFERENCES %i (`id`) ON DELETE CASCADE
            )
            DEFAULT CHARSET=utf8 COLLATE utf8_general_ci",
                [$email, $name, $name, $name, $name, $phone, $address, $zipcode, "{$table_name}_fk_1",
                    $event_periods_table, "{$table_name}_fk_2", $customers_table],
            )
        );
    }

}


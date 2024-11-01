<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Tables\Customer;

use mysqli_result;
use Yoyaku\Domain\Customer\ZipCode;
use Yoyaku\Domain\ValueObject\String\Address;
use Yoyaku\Domain\ValueObject\String\Email;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Domain\ValueObject\String\Phone;
use Yoyaku\Infrastructure\Tables\ATable;

class CustomersTable extends ATable
{
    const TABLE = 'customers';

    /**
     * @return bool|int|mysqli_result|null
     */
    public static function build_table()
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $name = Name::MAX_LENGTH;
        $email = Email::MAX_LENGTH;
        $phone = Phone::MAX_LENGTH;
        $address = Address::MAX_LENGTH;
        $zipcode = ZipCode::MAX_LENGTH;

        return $wpdb->query(
            $wpdb->prepare("
                CREATE TABLE IF NOT EXISTS $table_name  (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `wp_id` bigint(20) unsigned NULL DEFAULT NULL,
                    `email` varchar(%d) NOT NULL,
                    `first_name` varchar(%d) NOT NULL,
                    `last_name` varchar(%d) NOT NULL,
                    `first_name_ruby` varchar(%d) NOT NULL default '',
                    `last_name_ruby` varchar(%d) NOT NULL default '',
                    `phone` varchar(%d) NOT NULL DEFAULT '',
                    `birthday` date NULL,
                    `address` varchar(%d) NOT NULL default '',
                    `zipcode` varchar(%d) NOT NULL default '',
                    `gender` ENUM('male', 'female', '') NOT NULL DEFAULT '',
                    `memo` text NOT NULL DEFAULT '',
                    `registered` DATETIME NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `email` (`email`),
                    UNIQUE KEY `id` (`id`))
                DEFAULT CHARSET=utf8 COLLATE utf8_general_ci",
                [$email, $name, $name, $name, $name, $phone, $address, $zipcode],
            )
        );
    }
}

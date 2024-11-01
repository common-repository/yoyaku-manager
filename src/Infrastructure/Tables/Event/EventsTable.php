<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Tables\Event;

use mysqli_result;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Domain\ValueObject\String\Url;
use Yoyaku\Infrastructure\Tables\ATable;

class EventsTable extends ATable
{
    const TABLE = 'events';

    /**
     * @return bool|int|mysqli_result|null
     */
    public static function build_table()
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $name = Name::MAX_LENGTH;
        $url = Url::MAX_LENGTH;

        return $wpdb->query(
            $wpdb->prepare("
                CREATE TABLE IF NOT EXISTS $table_name (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `name` varchar(%d) NOT NULL,
                    `min_time_to_close_booking` int(11) NOT NULL,
                    `min_time_to_cancel_booking` int(11) NOT NULL,
                    `max_tickets_per_booking` int(11) NOT NULL,
                    `use_approval_system` TINYINT(1) NOT NULL,
                    `show_worker` TINYINT(1) NOT NULL,
                    `redirect_url` varchar (%d) NOT NULL DEFAULT '',
                    `description` TEXT NOT NULL DEFAULT '',
                    `is_online_payment` TINYINT(1) NOT NULL,
                    PRIMARY KEY (`id`)
                )
                DEFAULT CHARSET=utf8 COLLATE utf8_general_ci",
                [$name, $url],
            )
        );
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Tables\Notification;

use mysqli_result;
use Yoyaku\Domain\Notification\NotificationTiming;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Infrastructure\Tables\ATable;
use Yoyaku\Infrastructure\Tables\Migration\MigrationsTable;

class NotificationsTable extends ATable
{
    const TABLE = 'notifications';

    /**
     * @return bool|int|mysqli_result|null
     */
    public static function build_table()
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $name = Name::MAX_LENGTH;

        return $wpdb->query(
            $wpdb->prepare("
                CREATE TABLE IF NOT EXISTS $table_name (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(%d) NOT NULL,
                    `subject` VARCHAR(%d) NOT NULL DEFAULT '',
                    `content` TEXT NOT NULL DEFAULT '',
                    `timing` ENUM('approved', 'canceled', 'disapproved', 'pending', 'scheduled') NOT NULL,
                    `days` INT(11) NULL,
                    `time` TIME NULL,
                    `is_before` TINYINT(1) NOT NULL DEFAULT '1',
                    PRIMARY KEY (`id`)
                )
                DEFAULT CHARSET=utf8 COLLATE utf8_general_ci",
                [$name, $name]
            )
        );
    }

    /**
     * 初期データを追加する
     * @return bool
     */
    public static function add_initial_rows()
    {
        global $wpdb;
        $table_name = static::get_table_name();
        $rows = static::get_initial_rows();
        $migrations_table_name = MigrationsTable::get_table_name();

        // MigrationsTable のレコード数で実行済みか判定する
        if (0 !== intval($wpdb->get_var("SELECT COUNT(*) FROM {$migrations_table_name}"))) {
            return true;
        }

        $result = [];
        foreach ($rows as $row) {
            $result[] = boolval($wpdb->insert($table_name, $row));
        }

        return !in_array(false, $result);
    }

    /**
     * 初期データを取得
     * @return array ['カラム名' => 'データ'] の配列
     */
    private static function get_initial_rows()
    {
        /* translators: %s is placeholder */
        $dear_msg = sprintf(__('Dear %s,', 'yoyaku-manager'), '%customer_full_name%');

        return [
            [
                'name' => __('Booking Approved', 'yoyaku-manager'),
                'timing' => NotificationTiming::APPROVED->value,
                'subject' => __('Thank you for your booking.', 'yoyaku-manager'),
                'content' =>
                    $dear_msg . "\n" .
                    __('Thank you for your booking.', 'yoyaku-manager') . "\n" .
                    __('The reservation details are as follows.', 'yoyaku-manager') . "\n\n" .
                    '%event_name%' . "\n" .
                    __('Event Date:', 'yoyaku-manager') . ' %event_start_datetime% ~ %event_end_datetime%' . "\n" .
                    __('Event Location:', 'yoyaku-manager') . ' %event_location%',
            ],
            [
                'name' => __('Booking Pending', 'yoyaku-manager'),
                'timing' => NotificationTiming::PENDING->value,
                'subject' => __('Thank you for your booking.', 'yoyaku-manager'),
                'content' => $dear_msg . "\n" .
                    __('Thank you for your booking.', 'yoyaku-manager') . "\n" .
                    __('This booking is waiting for a confirmation.', 'yoyaku-manager') . "\n\n" .
                    __('The reservation details are as follows.', 'yoyaku-manager') . "\n\n" .
                    '%event_name%' . "\n" .
                    __('Event Date:', 'yoyaku-manager') . ' %event_start_datetime% ~ %event_end_datetime%' . "\n" .
                    __('Event Location:', 'yoyaku-manager') . ' %event_location%',
            ],
            [
                'name' => __('Booking Canceled', 'yoyaku-manager'),
                'timing' => NotificationTiming::CANCELED->value,
                'subject' => __('Booking Canceled.', 'yoyaku-manager'),
                'content' => $dear_msg . "\n" .
                    sprintf(
                    /* translators: %s is placeholder */
                        __('Your %s event booking has been canceled.', 'yoyaku-manager'),
                        '%event_name%'
                    ) . "\n" .
                    __('We hope to have the opportunity to serve you again.', 'yoyaku-manager')
            ],
            [
                'name' => __('Booking Disapproved', 'yoyaku-manager'),
                'timing' => NotificationTiming::DISAPPROVED->value,
                'subject' => __('Booking Disapproved.', 'yoyaku-manager'),
                'content' => $dear_msg . "\n" .
                    sprintf(
                    /* translators: %s is placeholder */
                        __('Your %s event booking has been disapproved.', 'yoyaku-manager'),
                        '%event_name%'
                    ) . "\n" .
                    __('We hope to have the opportunity to serve you again.', 'yoyaku-manager')
            ],

            // scheduled
            [
                'name' => __('Event Next Day Reminder', 'yoyaku-manager'),
                'timing' => NotificationTiming::SCHEDULED->value,
                'subject' => '%event_name% ' . __('Event Reminder', 'yoyaku-manager'),
                'content' => $dear_msg . "\n" .
                    sprintf(
                    /* translators: %s is placeholder */
                        __('We would like to remind you that you have event tomorrow at %s.', 'yoyaku-manager'),
                        '%event_start_datetime%'
                    ) . "\n" .
                    __('We are waiting for you.', 'yoyaku-manager'),
                'days' => 1,
                'time' => '12:00:00',
                'is_before' => 1,
            ],
        ];
    }
}

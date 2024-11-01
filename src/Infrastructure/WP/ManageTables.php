<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\WP;

use InvalidArgumentException;
use Yoyaku\Infrastructure\Tables\Customer\CustomersTable;
use Yoyaku\Infrastructure\Tables\Event\EventBookingsTable;
use Yoyaku\Infrastructure\Tables\Event\EventPeriodsTable;
use Yoyaku\Infrastructure\Tables\Event\EventsTable;
use Yoyaku\Infrastructure\Tables\Event\EventTicketBookingsTable;
use Yoyaku\Infrastructure\Tables\Event\EventTicketsTable;
use Yoyaku\Infrastructure\Tables\Migration\MigrationsTable;
use Yoyaku\Infrastructure\Tables\Notification\EmailLogsTable;
use Yoyaku\Infrastructure\Tables\Notification\NotificationsEventsTable;
use Yoyaku\Infrastructure\Tables\Notification\NotificationsTable;
use Yoyaku\Infrastructure\Tables\Notification\ScheduledNotificationLogsTable;
use Yoyaku\Infrastructure\Tables\Worker\WorkersTable;

/**
 * アクティベーション時に実行するデータベースのフック
 * @throws InvalidArgumentException
 */
class ManageTables
{
    /**
     * テーブルを作成する
     * 外部キー制約があるテーブルは初期化する順番を考慮する必要がある
     */
    public static function create()
    {
        MigrationsTable::init();
        CustomersTable::init();
        WorkersTable::init();
        EventsTable::init();
        EventPeriodsTable::init();
        EventTicketsTable::init();
        EventBookingsTable::init();
        EventTicketBookingsTable::init();
        NotificationsTable::init();
        NotificationsEventsTable::init();
        EmailLogsTable::init();
        ScheduledNotificationLogsTable::init();

        NotificationsTable::add_initial_rows();
        MigrationsTable::add_initial_rows();
    }

    public static function migrate()
    {
    }

    /**
     * テーブルを全て削除する
     */
    public static function drop()
    {
        ScheduledNotificationLogsTable::drop();
        EmailLogsTable::drop();
        NotificationsEventsTable::drop();
        NotificationsTable::drop();
        EventTicketBookingsTable::drop();
        EventBookingsTable::drop();
        EventTicketsTable::drop();
        EventPeriodsTable::drop();
        EventsTable::drop();
        WorkersTable::drop();
        CustomersTable::drop();
        MigrationsTable::drop();
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Repository\Notification;

use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\Notification\ScheduledNotificationLogFactory;
use Yoyaku\Domain\Notification\ScheduledNotificationToSend;
use Yoyaku\Infrastructure\Repository\ARepository;
use Yoyaku\Infrastructure\Tables\Notification\NotificationsTable;
use Yoyaku\Infrastructure\Tables\Notification\ScheduledNotificationLogsTable;

class ScheduledNotificationLogRepository extends ARepository
{
    const FACTORY = ScheduledNotificationLogFactory::class;

    public function __construct()
    {
        $table = ScheduledNotificationLogsTable::get_table_name();
        parent::__construct($table);
    }

    /**
     * 送信履歴があるかチェックする 未送信の定期通知を送信する時に使うためのメソッド
     * @param Collection<ScheduledNotificationToSend> $scheduled_notification_to_send_list
     * @throws WpDbException
     */
    public function get_not_send_notifications($scheduled_notification_to_send_list)
    {
        global $wpdb;

        if (!$scheduled_notification_to_send_list->length()) {
            return new Collection();
        }

        $notifications_table = NotificationsTable::get_table_name();
        $id_set = [];
        foreach ($scheduled_notification_to_send_list->get_items() as $item) {
            $period_id = $item->get_event_period_id()->get_value();
            $notification_id = $item->get_notification_id()->get_value();
            $id_set[] = "({$period_id},{$notification_id})";
        }

        $where = '';
        if ($id_set) {
            $where .= ' AND (event_period_id, notification_id) in (' . implode(' , ', $id_set) . ')';
        }

        $rows = $wpdb->get_results("
            SELECT
                event_period_id,
                notification_id
            FROM $this->table snl
                LEFT JOIN $notifications_table n ON snl.notification_id = n.id
            WHERE 1=1 $where",
            ARRAY_A,
        );

        // 送信履歴がない通知を取得
        $result = [];
        foreach ($scheduled_notification_to_send_list->get_items() as $item) {
            $period_id = $item->get_event_period_id()->get_value();
            $notification_id = $item->get_notification_id()->get_value();
            $is_exist = false;
            foreach ($rows as $row) {
                if ($period_id == $row['event_period_id'] && $notification_id == $row['notification_id']) {
                    $is_exist = true;
                    break;
                }
            }

            if (!$is_exist) {
                $result[] = $item;
            }
        }

        return new Collection($result);
    }
}

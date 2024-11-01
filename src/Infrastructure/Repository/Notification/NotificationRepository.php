<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Repository\Notification;

use Yoyaku\Domain\Notification\NotificationFactory;
use Yoyaku\Domain\Notification\NotificationTiming;
use Yoyaku\Infrastructure\Repository\ARepository;
use Yoyaku\Infrastructure\Tables\Notification\NotificationsTable;

/**
 * Class NotificationRepository
 */
class NotificationRepository extends ARepository
{

    const FACTORY = NotificationFactory::class;

    public function __construct()
    {
        $table = NotificationsTable::get_table_name();
        parent::__construct($table);
    }

    public function filter($filter)
    {
        global $wpdb;

        $orderby = '';
        if (!empty($filter['orderby']) && $filter['orderby'] == 'name') {
            $order = '';
            if (!empty($filter['order'])) {
                $order = $this->get_order($filter['order']);
            }
            $orderby = "ORDER BY name $order";
        }

        $rows = $wpdb->get_results("
            SELECT
                id,
                name,
                subject,
                content,
                timing,
                days,
                time,
                is_before
            FROM $this->table
            $orderby",
            ARRAY_A
        );

        $orderby = $filter['orderby'];
        if ($orderby === 'timing') {
            usort($rows, [$this, 'cmp_by_timing']);
        }

        return call_user_func([static::FACTORY, 'create_collection'], $rows);
    }

    /**
     * 通知の配列をtiming順に並び替えるための比較関数
     * @param $a
     * @param $b
     * @return int
     */
    private function cmp_by_timing($a, $b)
    {
        $timing_priority = array_flip(NotificationTiming::values());
        if ($a['timing'] === $b['timing']) {
            if ($a['name'] === $b['name']) {
                return 0;
            } else {
                return ($a['name'] < $b['name']) ? -1 : 1;
            }
        } else {
            return ($timing_priority[$a['timing']] < $timing_priority[$b['timing']]) ? -1 : 1;
        }
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Repository\Notification;

use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\Notification\EmailLog;
use Yoyaku\Domain\Notification\EmailLogFactory;
use Yoyaku\Infrastructure\Repository\ARepository;
use Yoyaku\Infrastructure\Tables\Notification\EmailLogsTable;

/**
 * Class NotificationLogRepository
 */
class EmailLogRepository extends ARepository
{
    const FACTORY = EmailLogFactory::class;

    public function __construct()
    {
        $table = EmailLogsTable::get_table_name();
        parent::__construct($table);
    }


    /**
     * @param array $filter
     * @param bool $count
     * @return Collection|int
     * @throws WpDbException
     */
    public function filter($filter = [], $count = false)
    {
        global $wpdb;

        $where = '';
        if (isset($filter['sent'])) {
            $where .= $wpdb->prepare(' AND nl.sent = %d', $filter['sent']);
        }

        $limit = '';
        if (isset($filter['page'], $filter['per_page'])) {
            $limit = $this->get_limit(absint($filter['page']), absint($filter['per_page']));
        }

        if ($count) {
            return intval($wpdb->get_var(
                "SELECT COUNT(*) FROM $this->table nl WHERE 1=1 $where"
            ));
        } else {
            $rows = $wpdb->get_results("
                    SELECT *
                        FROM $this->table as nl
                    WHERE 1=1 {$where}
                    ORDER BY id DESC
                    {$limit}",
                ARRAY_A
            );

            return call_user_func([static::FACTORY, 'create_collection'], $rows);
        }
    }

    /**
     * 送信に失敗した通知を取得
     * @param $filter
     * @return Collection<EmailLog>
     * @throws WpDbException
     */
    public function get_undelivered_notifications($filter)
    {
        global $wpdb;

        $where = '';
        if (isset($filter['id'])) {
            $where .= $wpdb->prepare(' AND nl.id = %d', $filter['id']);
        }

        $rows = $wpdb->get_results(
            "SELECT nl.* FROM $this->table nl WHERE nl.sent = 0 $where",
            ARRAY_A
        );

        return call_user_func([static::FACTORY, 'create_collection'], $rows);
    }

    /**
     * @param $email_logs Collection<EmailLog>
     * @return bool|int 追加した件数. 失敗した場合はfalse
     */
    public function bulk_add($email_logs)
    {
        global $wpdb;

        if (!$email_logs->length()) {
            return 0;
        }

        $query = "INSERT INTO {$this->table} (`customer_id`, `sent_datetime`, `sent`, `to`, `subject`, `content`) VALUES ";
        $insert_values = [];
        /** @var EmailLog $log */
        foreach ($email_logs->get_items() as $log) {
            $insert_values[] = $wpdb->prepare("(%d, %s, %d, %s, %s, %s)",
                $log->get_customer_id()->get_value(),
                $log->get_sent_datetime()->get_value_in_utc(),
                $log->get_sent(),
                $log->get_to()->get_value(),
                $log->get_subject()->get_value(),
                $log->get_content()->get_value(),
            );
        }
        $query .= implode(',', $insert_values);
        return $wpdb->query($query);
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Repository\Customer;

use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\Customer\Customer;
use Yoyaku\Domain\Customer\CustomerFactory;
use Yoyaku\Domain\Worker\Worker;
use Yoyaku\Infrastructure\Repository\ARepository;
use Yoyaku\Infrastructure\Tables\Customer\CustomersTable;

/**
 * UsersTableのCustomerデータを管理するリポジトリークラス
 */
class CustomerRepository extends ARepository
{
    const FACTORY = CustomerFactory::class;

    public function __construct()
    {
        $table = CustomersTable::get_table_name();
        parent::__construct($table);
    }

    /**
     * wp_idがマッチするユーザーを取得する
     * @param int $wp_id
     * @return Customer|Worker
     * @throws DataNotFoundException
     */
    public function get_by_wp_id($wp_id)
    {
        return $this->get('wp_id', $wp_id);
    }

    /**
     * メールでユーザー情報を取得する
     * @param string $email
     * @param bool $lock 行ロックをかける場合はtrue
     * @return Customer 存在する場合はUserオブジェクト, 存在しない時はfalse
     * @throws DataNotFoundException
     */
    public function get_by_email($email, $lock = false)
    {
        global $wpdb;

        if ($lock) {
            $query = $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE email = LOWER(%s) FOR UPDATE",
                $email
            );
        } else {
            $query = $wpdb->prepare("SELECT * FROM {$this->table} WHERE email = LOWER(%s)", $email);
        }
        $row = $wpdb->get_row($query, ARRAY_A);
        if (!$row) {
            throw new DataNotFoundException();
        }

        return call_user_func([static::FACTORY, 'create'], $row);
    }

    /**
     * メールでユーザーの存在確認をする
     * @param string $email
     * @return bool 存在する場合はtrue, 存在しない時はfalse
     */
    public function exists_by_email($email)
    {
        try {
            return !!$this->get_by_email($email);
        } catch (DataNotFoundException) {
            return false;
        }
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
        if (!empty($filter['id__in'])) {
            $where .= ' AND c.id IN (' . implode(',', wp_parse_id_list($filter['id__in'])) . ')';
        }
        if (!empty($filter['search'])) {
            $where .= $this->get_search_sql($filter['search']);
        }

        $orderby = 'ORDER BY id DESC';
        if (!empty($filter['orderby'])) {
            if ($filter['orderby'] == 'name') {
                $order_column = 'CONCAT(c.first_name, " ", c.last_name)';
            } else {
                $order_column = $filter['orderby'];
            }
            $order = '';
            if (!empty($filter['order'])) {
                $order = $this->get_order($filter['order']);
            }
            $orderby = "ORDER BY $order_column $order";
        }

        $limit = '';
        if (isset($filter['page'], $filter['per_page'])) {
            $limit = $this->get_limit(absint($filter['page']), absint($filter['per_page']));
        }

        if ($count) {
            return intval($wpdb->get_var(
                "SELECT COUNT(*) FROM $this->table c WHERE 1=1 $where",
            ));
        } else {
            $rows = $wpdb->get_results(
                "SELECT 
                    c.id as id,
                    c.wp_id as wp_id,
                    c.first_name as first_name,
                    c.last_name as last_name,
                    c.first_name_ruby as first_name_ruby,
                    c.last_name_ruby as last_name_ruby,
                    c.email as email,
                    c.phone as phone,
                    c.gender as gender,
                    c.birthday as birthday,
                    c.zipcode as zipcode,
                    c.address as address,
                    c.memo as memo,
                    c.registered as registered
                FROM $this->table as c
                WHERE 1=1 {$where}
                {$orderby}
                $limit",
                ARRAY_A
            );

            return call_user_func([static::FACTORY, 'create_collection'], $rows);
        }
    }

    protected function get_search_sql($text)
    {
        return parent::_get_search_sql(
            $text,
            ['c.first_name', 'c.last_name', 'c.first_name_ruby', 'c.last_name_ruby', 'c.email', 'c.memo', 'c.phone']
        );
    }
}

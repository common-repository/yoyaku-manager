<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Repository;

use InvalidArgumentException;
use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Infrastructure\WP\DB;

/**
 * リポジトリーの抽象クラス
 * 想定外の結果の場合は QueryExecutionError か NotFoundException をスローする
 */
class ARepository
{
    const FACTORY = '';
    protected DB $db;
    protected string $table;

    /**
     * @param string $table
     */
    public function __construct($table = '')
    {
        $this->db = new DB();
        $this->table = $table;
    }

    /**
     * @return Collection
     * @throws InvalidArgumentException
     */
    public function all()
    {
        global $wpdb;
        $rows = $wpdb->get_results("SELECT * FROM $this->table", ARRAY_A);
        return call_user_func([static::FACTORY, 'create_collection'], $rows);
    }

    /**
     * 条件に合うデータを取得する
     * @param $column_name
     * @param $value
     * @param string $format
     * @return mixed
     * @throws DataNotFoundException
     */
    public function get($column_name, $value, $format = '%d')
    {
        global $wpdb;

        $query = $wpdb->prepare("SELECT * FROM $this->table WHERE {$column_name}={$format}", $value);
        $row = $wpdb->get_row($query, ARRAY_A);
        if (!$row) {
            throw new DataNotFoundException();
        }
        return call_user_func([static::FACTORY, 'create'], $row);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws DataNotFoundException
     */
    public function get_by_id($id)
    {
        return $this->get('id', $id);
    }

    /**
     * id = $id のレコードの存在をチェックする
     * @param int $id
     * @param string $column_name
     * @return bool 存在する場合は true, 存在しない場合は false
     */
    public function exists($id, $column_name = 'id')
    {
        global $wpdb;

        return !!$wpdb->get_var(
            $wpdb->prepare("SELECT id FROM $this->table WHERE $column_name = %d", $id)
        );
    }

    /**
     * @param $order
     * @return string
     */
    protected function get_order($order)
    {
        return 'ASC' === strtoupper($order) ? 'ASC' : 'DESC';
    }

    /**
     * @param int $page
     * @param int $per_page
     * @return string
     */
    protected function get_limit($page, $per_page)
    {
        $offset = ($page - 1) * $per_page;
        return "LIMIT {$per_page} OFFSET {$offset}";
    }

    /**
     * Used internally to generate an SQL string for searching across multiple columns.
     * @param string $search Search string.
     * @param string[] $columns Array of columns to search.
     * @return string Search SQL.
     */
    protected function _get_search_sql($search, $columns)
    {
        global $wpdb;

        $like = '%' . $wpdb->esc_like($search) . '%';

        $searches = [];
        foreach ($columns as $column) {
            $searches[] = $wpdb->prepare("$column LIKE %s", $like);
        }

        return ' AND (' . implode(' OR ', $searches) . ')';
    }

    /**
     * @param mixed $entity
     * @return int idを返す
     * @throws WpDbException
     */
    public function add_by_entity($entity)
    {
        $res = $this->db->insert($this->table, $entity->to_table_data());
        if ($res === 0) {
            throw new WpDbException('Add no data');
        }
        return $this->db->last_insert_id();
    }

    /**
     * @param array $data
     * @param array $where
     * @return int The number of rows updated
     * @throws WpDbException
     */
    public function update($data, $where)
    {
        return $this->db->update($this->table, $data, $where);
    }

    /**
     * @param int $id
     * @param mixed $entity
     * @return int The number of rows updated
     * @throws WpDbException
     */
    public function update_by_entity($id, $entity)
    {
        $data = $entity->to_table_data();
        return $this->update($data, ['id' => $id]);
    }

    /**
     * データを削除する。削除対象のデータがない場合の戻り値は0
     * @param array $where 連想配列
     * @return int The number of rows deleted.
     * @throws WpDbException
     */
    public function delete($where)
    {
        return $this->db->delete($this->table, $where);
    }
}

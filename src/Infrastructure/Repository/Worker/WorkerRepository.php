<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Repository\Worker;

use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\Worker\Worker;
use Yoyaku\Domain\Worker\WorkerFactory;
use Yoyaku\Infrastructure\Repository\ARepository;
use Yoyaku\Infrastructure\Tables\Worker\WorkersTable;


class WorkerRepository extends ARepository
{
    const FACTORY = WorkerFactory::class;

    public function __construct()
    {
        $table = WorkersTable::get_table_name();
        parent::__construct($table);
    }

    /**
     * @param array $ids
     * @return Collection
     * @throws WpDbException
     */
    public function filter_by_ids($ids)
    {
        global $wpdb;

        if (!$ids) {
            return new Collection();
        }

        $where = ' AND w.id IN (' . implode(', ', wp_parse_id_list($ids)) . ')';
        $rows = $wpdb->get_results("
            SELECT * FROM $this->table w WHERE 1=1 $where",
            ARRAY_A,
        );

        $result = [];
        foreach ($rows as $row) {
            $result[$row['id']] = call_user_func([static::FACTORY, 'create'], $row);
        }
        return new Collection($result);
    }

    /**
     * last_insert_id は 主キーが AUTO INCREMENT の時のみ有効で、
     * idを指定してinsertした場合は使えない（MySQL）そのためidを明示的に返している。
     * @param Worker $entity
     * @return int idを返す
     * @throws WpDbException
     */
    public function add_by_entity($entity)
    {
        $res = $this->db->insert($this->table, $entity->to_table_data());
        if ($res === 0) {
            throw new WpDbException('Add no data');
        }
        return $entity->get_id()->get_value();
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Domain\Common;

use Yoyaku\Domain\Collection\Collection;

abstract class AFactory
{
    const FACTORY = '';

    /**
     * @param array $fields
     * @return mixed
     */
    abstract public static function create($fields);

    /**
     * get_results()で取得した複数のデータからエンティティーのコレクションを作成する
     * @param array $rows
     * @return Collection
     */
    public static function create_collection($rows)
    {
        assert(empty(static::FACTORY), 'Factory is not defined');

        $collection = new Collection();
        foreach ($rows as $row) {
            $collection->add_item(static::create($row));
        }
        return $collection;
    }
}

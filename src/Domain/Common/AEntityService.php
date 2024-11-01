<?php declare(strict_types=1);

namespace Yoyaku\Domain\Common;

use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Infrastructure\Repository\ARepository;

abstract class AEntityService
{
    /**
     * @var string 名前空間を含むクラス名
     */
    protected string $factory;
    protected ARepository $repo;

    public function __construct($repo, $factory)
    {
        $this->repo = $repo;
        $this->factory = $factory;
    }

    /**
     * テーブルにデータを追加し、追加されたデータ返す
     * @param array $fields
     * @return int
     * @throws WpDbException
     */
    public function add($fields)
    {
        $entity = call_user_func([$this->factory, 'create'], $fields);
        return $this->repo->add_by_entity($entity);
    }

    /**
     * @param int $id
     * @param $update_fields
     * @return int
     * @throws DataNotFoundException
     * @throws WpDbException
     */
    public function update($id, $update_fields)
    {
        $entity = $this->repo->get_by_id($id);
        $fields = array_merge($entity->to_array(), $update_fields);
        $new_entity = call_user_func([$this->factory, 'create'], $fields);
        return $this->repo->update_by_entity($id, $new_entity);
    }
}

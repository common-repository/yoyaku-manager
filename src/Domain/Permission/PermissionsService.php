<?php declare(strict_types=1);

namespace Yoyaku\Domain\Permission;

use Yoyaku\Domain\WpUser\AWpUser;
use Yoyaku\Domain\WpUser\WpAdmin;
use Yoyaku\Domain\WpUser\WpGuest;

/**
 * ユーザーがオブジェクトを操作(readやwriteなど)する権限があるかチェックするクラス
 */
class PermissionsService
{
    /**
     * ユーザーの権限をチェックする。Adminは全権限を持つ
     * @param AWpUser $user
     * @param string $entity
     * @param string $permission
     * @return bool
     */
    public function user_can($user, $entity, $permission)
    {
        if ($user instanceof WpAdmin) {
            return true;
        }
        return $this->check_permissions($user, $entity, $permission);
    }

    /**
     * @param AWpUser $user
     * @param string $entity
     * @param string $permission
     * @return bool
     */
    private function check_permissions($user, $entity, $permission)
    {
        if ($user instanceof WpGuest) {
            return false;
        }

        // チェックしたい権限名を作成
        $capability = "yoyaku_{$permission}_{$entity}";
        return user_can($user->get_id()->get_value(), $capability);
    }

    /**
     * @param AWpUser $user
     * @param string $entity
     * @return bool
     */
    public function user_can_read($user, $entity)
    {
        return $this->user_can($user, $entity, 'read');
    }

    /**
     * @param AWpUser $user
     * @param $entity
     * @return bool
     */
    public function user_can_write($user, $entity)
    {
        return $this->user_can($user, $entity, 'write');
    }

    /**
     * @param AWpUser $user
     * @param $entity
     * @return bool
     */
    public function user_can_delete($user, $entity)
    {
        return $this->user_can($user, $entity, 'delete');
    }
}

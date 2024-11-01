<?php declare(strict_types=1);

namespace Yoyaku\Domain\WpUser;

use Yoyaku\Domain\UserRole\UserRole;

/**
 * yoyaku-worker権限を持つユーザークラス Entity
 */
class WpYoyakuWorker extends BaseWpOrganizer
{
    public function get_role()
    {
        return UserRole::WORKER;
    }
}

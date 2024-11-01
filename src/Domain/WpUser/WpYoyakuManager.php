<?php declare(strict_types=1);

namespace Yoyaku\Domain\WpUser;

use Yoyaku\Domain\UserRole\UserRole;

/**
 * yoyaku-manager権限を持つユーザークラス Entity
 */
class WpYoyakuManager extends BaseWpOrganizer
{
    public function get_role()
    {
        return UserRole::MANAGER;
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Domain\WpUser;

use Yoyaku\Domain\UserRole\UserRole;

/**
 * wpのAdmin権限を持つユーザークラス Entity
 */
class WpAdmin extends BaseWpOrganizer
{
    public function get_role()
    {
        return UserRole::ADMIN;
    }
}

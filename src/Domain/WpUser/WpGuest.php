<?php declare(strict_types=1);

namespace Yoyaku\Domain\WpUser;

use Yoyaku\Domain\UserRole\UserRole;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\String\Email;
use Yoyaku\Domain\ValueObject\String\Name;

/**
 * ゲスト（未ログイン）ユーザー Entity
 */
class WpGuest extends AWpUser
{
    public function __construct()
    {
        $id = new Id(0);
        $name = new Name('');
        $email = new Email('');
        parent::__construct($id, $email, $name);
    }

    public function get_role()
    {
        return UserRole::GUEST;
    }
}

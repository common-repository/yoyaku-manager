<?php declare(strict_types=1);

namespace Yoyaku\Domain\WpUser;

use Yoyaku\Domain\Common\AFactory;
use Yoyaku\Domain\UserRole\UserRole;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\String\Email;
use Yoyaku\Domain\ValueObject\String\Name;

/**
 * 権限グループに応じてユーザーのインスタンスを生成するファクトリー
 */
class WpUserFactory extends AFactory
{
    /**
     * @param $fields
     * @return AWpUser
     */
    public static function create($fields)
    {
        $role = $fields['role'] ?? '';

        return match ($role) {
            UserRole::ADMIN => new WpAdmin(
                new Id($fields['id']),
                new Email($fields['user_email']),
                new Name($fields['display_name']),
            ),
            UserRole::MANAGER => new WpYoyakuManager(
                new Id($fields['id']),
                new Email($fields['user_email']),
                new Name($fields['display_name']),
            ),
            UserRole::WORKER => new WpYoyakuWorker(
                new Id($fields['id']),
                new Email($fields['user_email']),
                new Name($fields['display_name']),
            ),
            default => new WpGuest(),
        };
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Domain\UserRole;

use WP_User;

/**
 * yoyakuで扱う権限グループのクラス
 */
enum UserRole: string
{
    case ADMIN = 'admin';
    case WORKER = 'worker';
    case MANAGER = 'manager';
    case GUEST = 'guest';

    /**
     * 現在のユーザーのyoyaku権限グループを返す
     * @param WP_User $wp_user
     * @return UserRole
     */
    public static function get_user_yoyaku_role($wp_user)
    {
        // wpの管理者か、特権管理者ならadmin
        if (in_array('administrator', $wp_user->roles, true) || is_super_admin($wp_user->ID)) {
            return UserRole::ADMIN;
        }

        if (in_array('yoyaku-manager', $wp_user->roles, true)) {
            return UserRole::MANAGER;
        }

        if (in_array('yoyaku-worker', $wp_user->roles, true)) {
            return UserRole::WORKER;
        }

        return UserRole::GUEST;
    }

    public function get_wp_role_name(): string
    {
        if ($this == UserRole::MANAGER || $this == UserRole::WORKER) {
            return 'yoyaku-' . $this->value;
        } else if ($this == UserRole::ADMIN) {
            return 'administrator';
        } else {
            return '';
        }
    }

    public function translated(): string
    {
        if ($this == UserRole::MANAGER || $this == UserRole::WORKER) {
            return 'Yoyaku ' . ucfirst($this->value);
        } else if ($this == UserRole::ADMIN) {
            return translate_user_role('Administrator');
        } else {
            return '';
        }
    }
}

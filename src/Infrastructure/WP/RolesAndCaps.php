<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\WP;

use Yoyaku\Domain\UserRole\UserRole;

/**
 * 権限グループのクラス
 * メモ) read権限 Dashboard, Users > Your Profile の画面が閲覧できる
 * @see https://wordpress.org/documentation/article/roles-and-capabilities/#read
 */
class RolesAndCaps
{
    /**
     * 権限の配列
     * settingsの read/write はadministratorのみ可能
     * @var array
     */
    private static $capabilities = [
        'yoyaku_read_menu',  // メニューの表示権限 add_menu_page()で使用
        'yoyaku_read_calendar',  // カレンダー
        'yoyaku_read_events',  // イベント
        'yoyaku_read_bookings',  // 予約
        'yoyaku_read_customers',  // お客様
        'yoyaku_read_notifications',  // 通知
        'yoyaku_read_emaillogs',  // メールログ
        'yoyaku_read_workers',  // 従業員

        'yoyaku_write_events',
        'yoyaku_write_bookings',
        'yoyaku_write_workers',
        'yoyaku_write_customers',
        'yoyaku_write_notifications',
        'yoyaku_write_emaillogs',

        'yoyaku_delete_events',
        'yoyaku_delete_bookings',
        'yoyaku_delete_workers',
        'yoyaku_delete_customers',
        'yoyaku_delete_notifications',
        'yoyaku_delete_emaillogs',
    ];

    /**
     * 権限グループと権限の設定をする
     */
    public static function activate()
    {
        self::add_roles();
        self::update_caps();
        self::add_all_caps_to_administrator();
    }

    /**
     * yoyakuの権限グループを取得
     * @return array
     */
    public static function get_all()
    {
        $worker = [
            'name' => UserRole::WORKER->get_wp_role_name(),
            'display_name' => __('Yoyaku Worker', 'yoyaku-manager'),
            'capabilities' => [
                'read' => true,

                'yoyaku_read_menu' => true,
                'yoyaku_read_calendar' => true,
                'yoyaku_read_events' => true,
                'yoyaku_read_bookings' => true,
                'yoyaku_read_customers' => true,
                'yoyaku_read_workers' => true,
                'yoyaku_read_notifications' => true,
                'yoyaku_read_emaillogs' => true,
            ],
        ];

        $manager = [
            'name' => UserRole::MANAGER->get_wp_role_name(),
            'display_name' => __('Yoyaku Manager', 'yoyaku-manager'),
            'capabilities' => array_merge(
                $worker['capabilities'],
                [
                    'yoyaku_write_events' => true,
                    'yoyaku_write_bookings' => true,
                    'yoyaku_write_customers' => true,
                    'yoyaku_write_workers' => true,
                    'yoyaku_write_notifications' => true,
                    'yoyaku_write_emaillogs' => true,

                    'yoyaku_delete_events' => true,
                    'yoyaku_delete_bookings' => true,
                    'yoyaku_delete_customers' => true,
                    'yoyaku_delete_workers' => true,
                    'yoyaku_delete_notifications' => true,
                    'yoyaku_delete_emaillogs' => true,
                ]
            )
        ];

        return [$worker, $manager];
    }

    /**
     * 権限グループ(role)を追加
     */
    private static function add_roles()
    {
        foreach (RolesAndCaps::get_all() as $role) {
            if (!wp_roles()->is_role($role['name'])) {
                add_role($role['name'], $role['display_name'], $role['capabilities']);
            }
        }
    }

    /**
     * 権限グループ(role)を削除
     */
    public static function remove_roles()
    {
        global $wp_roles;
        foreach (RolesAndCaps::get_all() as $role) {
            if (wp_roles()->is_role($role['name'])) {
                $wp_roles->remove_role($role['name']);
            }
        }
    }

    /**
     * 権限(cap)を更新
     */
    private static function update_caps()
    {
        foreach (RolesAndCaps::get_all() as $_role) {
            $role = get_role($_role['name']);
            if (is_null($role)) {
                continue;
            }
            foreach (array_keys($_role['capabilities']) as $cap) {
                if (!$role->has_cap($cap)) {
                    $role->add_cap($cap);
                }
            }
        }
    }

    /**
     * administratorにyoyakuの全ての権限(capability)を追加
     */
    private static function add_all_caps_to_administrator()
    {
        $admin_role = get_role('administrator');
        if ($admin_role !== null) {
            foreach (self::$capabilities as $cap) {
                $admin_role->add_cap($cap);
            }
        }
    }
}

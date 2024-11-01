<?php

namespace Yoyaku;

use DI\Container;
use Yoyaku\Application\Routes\Routes;
use Yoyaku\Domain\Setting\SettingsService;
use Yoyaku\Domain\UserRole\UserRole;
use Yoyaku\Infrastructure\WP\ActivateSettings;
use Yoyaku\Infrastructure\WP\BlockService\CancelBookingBlockService;
use Yoyaku\Infrastructure\WP\BlockService\EventBlockService;
use Yoyaku\Infrastructure\WP\ManageTables;
use Yoyaku\Infrastructure\WP\RolesAndCaps;
use Yoyaku\Infrastructure\WP\WPMenu\MenuHandler;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// プラグインフォルダへの絶対パス
if (!defined('YOYAKU_PATH')) {
    define('YOYAKU_PATH', __DIR__);
}

if (!defined('YOYAKU_PLUGIN_FILE')) {
    define('YOYAKU_PLUGIN_FILE', __FILE__);
}

if (!defined('YOYAKU_URL')) {
    define('YOYAKU_URL', plugin_dir_url(__FILE__));
}

if (!defined('YOYAKU_VERSION')) {
    define('YOYAKU_VERSION', '1.0.5');
}

// プラグインの設定名
if (!defined('YOYAKU_SETTING_NAME')) {
    define('YOYAKU_SETTING_NAME', 'yoyaku_settings');
}

// プラグインのapiエンドポイント
if (!defined('YOYAKU_ROUTE_NAMESPACE')) {
    define('YOYAKU_ROUTE_NAMESPACE', 'yoyaku/v1');
}

// 製品のID
if (!defined('YOYAKU_MANAGER_ITEM_ID')) {
    define('YOYAKU_MANAGER_ITEM_ID', 377);
}

require_once YOYAKU_PATH . '/vendor/autoload.php';

/**
 * フリー版Pro版共通のプラグインクラス
 */
class YoyakuBase
{
    /**
     * REST API のエンドポイントを追加
     */
    public static function register_routes()
    {
        $definitions = require YOYAKU_PATH . '/src/Infrastructure/container-definitions.php';
        Routes::init(new Container($definitions));
    }

    /**
     * プラグインの初期化
     */
    public static function init()
    {
        load_plugin_textdomain(
            'yoyaku-manager',
            false,
            plugin_basename(__DIR__) . '/languages'
        );

        $yoyaku_role = UserRole::get_user_yoyaku_role(wp_get_current_user());
        // 権限グループでログインしている場合はメニューを初期化する
        if (in_array($yoyaku_role, [UserRole::ADMIN, UserRole::MANAGER, UserRole::WORKER])) {
            // admin menuを初期化
            $menu_handler = new MenuHandler();
            $menu_handler->add_wp_menu();

            // Gutenbergブロックを追加
            foreach (glob(plugin_dir_path(__FILE__) . 'build/gutenberg-blocks/*') as $block) {
                $block_type = register_block_type($block);
                if (!empty($block_type->editor_script_handles)) {
                    // ブロック毎に翻訳をセットする
                    wp_set_script_translations(
                        $block_type->editor_script_handles[0],
                        'yoyaku-manager',
                        YOYAKU_PATH . '/languages'
                    );
                }
            }

            // GutenbergにYoyakuのカテゴリーを追加
            add_filter('block_categories_all', ['Yoyaku\YoyakuBase', 'add_yoyaku_block_category'], 10, 2);
        }

        CancelBookingBlockService::add_hooks();
        EventBlockService::add_hooks();
    }

    public static function add_yoyaku_block_category($block_categories, $block_editor_context)
    {
        return array_merge(
            [
                ['slug' => 'yoyaku-blocks', 'title' => 'Yoyaku'],
            ],
            $block_categories
        );
    }

    public static function upgrade_plugin()
    {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        ManageTables::migrate();
    }

    /**
     * @param $network_wide
     */
    public static function activation($network_wide)
    {
        ManageTables::create();
        RolesAndCaps::activate();
        ActivateSettings::activate();
    }

    public static function deletion()
    {
        $settings_ds = SettingsService::get_instance();
        if ($settings_ds->get('delete_content')) {
            ManageTables::drop();
            RolesAndCaps::remove_roles();

            // 設定削除
            delete_option(YOYAKU_SETTING_NAME);
            // for site options in Multisite
            delete_site_option(YOYAKU_SETTING_NAME);
        }
    }
}

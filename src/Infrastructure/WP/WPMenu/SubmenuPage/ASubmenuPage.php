<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\WP\WPMenu\SubmenuPage;

use Yoyaku\Domain\Setting\SettingsService;

abstract class ASubmenuPage
{
    public string $page_title;
    public string $menu_title;
    public string $capability;
    public string $menu_slug;

    public function __construct($page_title, $menu_title, $capability, $menu_slug)
    {
        $this->page_title = $page_title;
        $this->menu_title = $menu_title;
        $this->capability = $capability;
        $this->menu_slug = $menu_slug;
    }

    /**
     * サブメニューページを表示する
     */
    public function render()
    {
        $script_name = 'yoyaku_booking_scripts';

        // reactアプリの依存パッケージが定義されているファイルを読み込む
        $asset_file = require(YOYAKU_PATH . '/buildre/index.asset.php');

        // reactのビルドファイルを出力用のキューに入れる
        wp_enqueue_script(
            $script_name,
            YOYAKU_URL . 'buildre/index.js',
            $asset_file['dependencies'],
            $asset_file['version'],
            false,
        );


        wp_enqueue_style(
            $script_name,
            YOYAKU_URL . 'buildre/style-index.css',
            ['wp-components'],
            $asset_file['version'],
        );

        // react-big-calendar.cssのビルドファイル
        wp_enqueue_style(
            "react-big-calendar",
            YOYAKU_URL . 'buildre/index.css',
            ['wp-components'],
            $asset_file['version'],
        );

        // 設定値系のデータをjsの変数に定義する
        $settings_ds = SettingsService::get_instance();
        wp_localize_script($script_name, 'wpYoyakuSettings', $settings_ds->get_settings_for_admin());

        // reactアプリ用翻訳ファイルを読み込む
        wp_set_script_translations($script_name, 'yoyaku-manager', YOYAKU_PATH . '/languages');

        // view.phpで使う変数
        $page = $this->menu_slug;
        include YOYAKU_PATH . '/includes/view.php';
    }
}

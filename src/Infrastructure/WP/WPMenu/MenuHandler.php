<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\WP\WPMenu;

use Yoyaku\Infrastructure\WP\WPMenu\SubmenuPage\Bookings;
use Yoyaku\Infrastructure\WP\WPMenu\SubmenuPage\Customers;
use Yoyaku\Infrastructure\WP\WPMenu\SubmenuPage\EmailLogs;
use Yoyaku\Infrastructure\WP\WPMenu\SubmenuPage\Events;
use Yoyaku\Infrastructure\WP\WPMenu\SubmenuPage\Notifications;
use Yoyaku\Infrastructure\WP\WPMenu\SubmenuPage\Settings;
use Yoyaku\Infrastructure\WP\WPMenu\SubmenuPage\Workers;

/**
 * wpの管理画面にメニューを追加する
 */
class MenuHandler
{
    /**
     * メニューを追加するフックを登録
     */
    public function add_wp_menu()
    {
        add_action('admin_menu', [$this, 'add_pages']);
    }

    /**
     * ページを追加
     */
    public function add_pages()
    {
        add_menu_page(
            'Yoyaku Booking', // タイトルタグ<title>に表示されるテキスト
            'Yoyaku', // メニューのタイトル
            'yoyaku_read_menu', // 権限
            'yoyaku', // 左メニューのスラッグ名. URLのパラメータに使われる
            '',
            YOYAKU_URL . 'assets/img/menu-icon.svg',
        );

        $submenu_pages = [
            new Events(),
            new Bookings(),
            new Customers(),
            new Notifications(),
            new Workers(),
            new EmailLogs(),
            new Settings(),
        ];
        foreach ($submenu_pages as $page) {
            add_submenu_page(
                'yoyaku',
                $page->page_title,
                $page->menu_title,
                $page->capability,
                $page->menu_slug,
                fn() => $page->render(),
            );
        }

        // 自動でadd_menu_page()の第４引数と同じスラッグのsubmenu_pageが作成されるため削除する
        remove_submenu_page('yoyaku', 'yoyaku');
    }
}

<?php
/**
 * Plugin Name: Yoyaku Manager
 * Plugin URI: https://yoyaku-manager.com
 * Description: Yoyaku Manager is for managing events and bookings, etc.
 * Version: 1.0.5
 * Requires at least: 6.5
 * Requires PHP: 8.1
 * Tested up to: 6.6
 * Author: Allneko Club
 * Author URI: https://allneko.club
 * Domain Path: /languages
 * Text Domain: yoyaku-manager
 * License: GPLv2 or later
 */

namespace Yoyaku;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once 'yoyaku-manager-base.php';

final class YoyakuFree extends YoyakuBase
{
    /**
     * @param $network_wide
     */
    public static function activation($network_wide)
    {
        if (class_exists('Yoyaku\YoyakuManagerPro')) {
            $url = admin_url('plugins.php');
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(sprintf(
            /* translators: %1$s plugins url, %2$s a tag */
                esc_html__(
                    'Please deactivate and delete Yoyaku Manager Pro before activating Yoyaku Manager. %1$sReturn to the Dashboard%2$s.',
                    'yoyaku-manager'
                ),
                '<a href="' . esc_url_raw($url) . '">', '</a>'
            ));
        }

        parent::activation($network_wide);
    }
}

// APIエンドポイントを追加
add_action('rest_api_init', ['Yoyaku\YoyakuFree', 'register_routes']);

// プラグインの初期化
add_action('plugins_loaded', ['Yoyaku\YoyakuFree', 'init']);

// プラグイン更新時
add_action('upgrader_process_complete', ['Yoyaku\YoyakuFree', 'upgrade_plugin']);

// 有効化
register_activation_hook(__FILE__, ['Yoyaku\YoyakuFree', 'activation']);

// アンインストール
register_uninstall_hook(__FILE__, ['Yoyaku\YoyakuFree', 'deletion']);

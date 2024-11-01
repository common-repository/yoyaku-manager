<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\WP\BlockService;

use Exception;
use WP_Block_Supports;
use Yoyaku\Domain\Setting\SettingsService;

/**
 * ブロックのレンダリングに必要なスクリプトなどを定義するためのクラス
 * 注) 1つの記事内に同じブロックを複数使うことはできない
 */
abstract class ABlockService
{
    protected static string $script_name = 'yoyaku_booking_scripts';
    protected static string $block_name = '';

    /**
     * wp-includes/class-wp-block-supports.php 改造
     * @param $extra_attributes
     * @return array|string ブロックのattributes
     */
    protected static function get_block_wrapper_attributes($extra_attributes = array())
    {
        $new_attributes = WP_Block_Supports::get_instance()->apply_block_supports();

        if (empty($new_attributes) && empty($extra_attributes)) {
            return '';
        }

        // This is hardcoded on purpose.
        // We only support a fixed list of attributes.
        $attributes_to_merge = array('style', 'class', 'id');
        $attributes = array();
        foreach ($attributes_to_merge as $attribute_name) {
            if (empty($new_attributes[$attribute_name]) && empty($extra_attributes[$attribute_name])) {
                continue;
            }

            if (empty($new_attributes[$attribute_name])) {
                $attributes[$attribute_name] = $extra_attributes[$attribute_name];
                continue;
            }

            if (empty($extra_attributes[$attribute_name])) {
                $attributes[$attribute_name] = $new_attributes[$attribute_name];
                continue;
            }

            $attributes[$attribute_name] = $extra_attributes[$attribute_name] . ' ' . $new_attributes[$attribute_name];
        }

        foreach ($extra_attributes as $attribute_name => $value) {
            if (!in_array($attribute_name, $attributes_to_merge, true)) {
                $attributes[$attribute_name] = $value;
            }
        }

        if (empty($attributes)) {
            return '';
        }

        $normalized_attributes = array();
        foreach ($attributes as $key => $value) {
            $normalized_attributes[$key] = esc_attr($value);
        }
        return $normalized_attributes;
    }

    /**
     * cssやjsを読み込むhooksを追加する
     * @return void
     */
    abstract protected static function add_hooks();

    /**
     * ショートコードで使うスタイルやjsファイル、js変数の定義をする
     * @param array $attributes ブロックのattributes
     * @return void
     */
    abstract protected static function prepare_block_scripts_and_styles($attributes);

    /**
     * スタイルやjsファイルの読み込みや、js変数の定義をする
     * @param array $attributes ブロックのattributes
     * @throws Exception
     */
    public static function prepare_scripts_and_styles($attributes)
    {
        $block_name = static::$block_name;
        $asset_file = require(YOYAKU_PATH . "/build/gutenberg-blocks/$block_name/view.asset.php");

        wp_enqueue_script(
            static::$script_name,
            YOYAKU_URL . "build/gutenberg-blocks/$block_name/view.js",
            $asset_file['dependencies'],
            $asset_file['version'],
            false,
        );

        $settings_ds = SettingsService::get_instance();
        wp_localize_script(static::$script_name, 'wpYoyakuSettings', $settings_ds->get_settings_for_front());
        wp_localize_script(static::$script_name, 'wpYoyakuBlockAttributes', $attributes);
        wp_set_script_translations(static::$script_name, 'yoyaku-manager', YOYAKU_PATH . '/languages');

        static::prepare_block_scripts_and_styles($attributes);
    }
}

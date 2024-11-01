<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\WP\BlockService;

use Exception;
use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\EventType\EventBooking\EventBookingQuery;

class CancelBookingBlockService extends ABlockService
{
    protected static string $block_name = 'cancel-booking';

    /**
     * キャンセルに必要なデータをjs変数に登録する
     * キャンセルできない場合は、messageを登録する。
     * @param array $attributes ブロックのattributes
     * @throws Exception
     */
    protected static function prepare_block_scripts_and_styles($attributes)
    {
        try {
            $query = new EventBookingQuery();
            $token = '';
            if (isset($_GET['token'])) {
                $token = sanitize_text_field(wp_unslash($_GET['token']));
            }
            $data = $query->get_cancel_data($token);
            wp_localize_script(self::$script_name, 'wpYoyakuCancelData', $data);

            wp_localize_script(
                self::$script_name,
                'wpYoyakuWrapperAttributes',
                self::get_block_wrapper_attributes()
            );

        } catch (DataNotFoundException) {
            wp_localize_script(
                self::$script_name,
                'wpYoyakuCancelData',
                ['error_message' => __("This booking was not found.", "yoyaku-manager")]
            );
        }
    }

    public static function add_hooks()
    {
        add_action('enqueue_block_assets', function () {
            $block_name = static::$block_name;
            $asset_file = require(YOYAKU_PATH . "/build/gutenberg-blocks/$block_name/view.asset.php");
            wp_enqueue_style(
                "$block_name-style-index",
                YOYAKU_URL . "build/gutenberg-blocks/$block_name/style-index.css",
                ['wp-components'],
                $asset_file['version'],
            );
        });
    }
}

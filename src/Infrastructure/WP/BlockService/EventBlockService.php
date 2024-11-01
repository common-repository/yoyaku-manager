<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\WP\BlockService;

use Exception;
use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Application\EventType\Event\EventQuery;
use Yoyaku\Domain\EventType\Event\Event;
use Yoyaku\Domain\Setting\SettingsService;
use Yoyaku\Infrastructure\Repository\EventType\EventRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventTicketRepository;

class EventBlockService extends ABlockService
{
    protected static string $block_name = 'event';

    /**
     * イベントデータのjs変数を定義する。不備がある場合は、error_messageを設定する。
     * @param array $attributes ブロックのattributes
     * @throws Exception
     */
    protected static function prepare_block_scripts_and_styles($attributes)
    {
        $settings = SettingsService::get_instance();
        $object_name = 'wpYoyakuEventData';
        try {
            if (empty($attributes['eventId'])) {
                wp_localize_script(
                    self::$script_name,
                    $object_name,
                    ['error_message' => __('Event ID is not set.', 'yoyaku-manager')]
                );
                return;
            }

            $event_repo = new EventRepository();
            $ticket_repo = new EventTicketRepository();
            $event_query = new EventQuery();
            $event_id = $attributes['eventId'];
            /** @var Event $event */
            $event = $event_repo->get_by_id($event_id);
            if ($event->get_is_online_payment() && !$settings->stripe_is_active()) {
                wp_localize_script(
                    self::$script_name,
                    $object_name,
                    ['error_message' => __('Stripe setting is insufficient.', 'yoyaku-manager')]
                );
                return;
            }

            // チケット存在チェック
            if (!$ticket_repo->filter(['event_id' => $event_id])->length()) {
                wp_localize_script(
                    self::$script_name,
                    $object_name,
                    ['error_message' => __('There are no tickets.', 'yoyaku-manager')]
                );
                return;
            }

            $event_periods = $event_query->filter_event_periods_for_front($event_id);
            $event->set_periods($event_periods);

            wp_localize_script(self::$script_name, $object_name, $event->to_array_for_customer());

        } catch (DataNotFoundException) {
            wp_localize_script(
                self::$script_name,
                $object_name,
                ['error_message' => __('No events found.', 'yoyaku-manager')]
            );
        } catch (WpDbException) {
            wp_localize_script(
                self::$script_name,
                $object_name,
                ['error_message' => __('Failed while retrieving event data.', 'yoyaku-manager')]
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
            if (!is_admin()) {
                wp_enqueue_style(
                    "$block_name-style-view",
                    YOYAKU_URL . "build/gutenberg-blocks/$block_name/style-view.css",
                    ['wp-components'],
                    $asset_file['version'],
                );
            }
        });
    }
}

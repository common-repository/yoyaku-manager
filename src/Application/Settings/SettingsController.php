<?php declare(strict_types=1);

namespace Yoyaku\Application\Settings;

use Exception;
use WP_REST_Request;
use WP_REST_Response;
use Yoyaku\Application\Common\AController;
use Yoyaku\Domain\Setting\SettingsService;

/**
 * プラグインに関連する設定を取得する
 */
class SettingsController extends AController
{
    /**
     * 管理者が設定ページを閲覧するときに使う
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function get_settings($request)
    {
        $settings = SettingsService::get_instance();
        return new WP_REST_Response($settings->get_all_settings());
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function update_settings($request)
    {
        $settings_ds = SettingsService::get_instance();
        $settings_fields = $request->get_params();
        $all_settings = $settings_ds->get_all_settings();
        foreach ($settings_fields as $key => $value) {
            $all_settings[$key] = $value;
        }

        $settings_ds->set_all_settings($all_settings);
        return new WP_REST_Response(['message' => 'success']);
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     * @throws Exception
     */
    public function get_front_settings($request)
    {
        $settings_ds = SettingsService::get_instance();
        return new WP_REST_Response($settings_ds->get_settings_for_front());
    }
}

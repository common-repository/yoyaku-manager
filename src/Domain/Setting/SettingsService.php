<?php declare(strict_types=1);

namespace Yoyaku\Domain\Setting;

use Exception;

class SettingsService
{
    private $settings_cache;
    private static SettingsService $singleton;

    private function __construct()
    {
        $this->update_cache();
    }

    public static function get_instance()
    {
        if (!isset(self::$singleton)) {
            self::$singleton = new SettingsService();
        }
        return self::$singleton;
    }

    /**
     * 設定を再取得する
     * @return void
     * @throws Exception
     */
    public function update_cache()
    {
        $this->settings_cache = json_decode(get_option(YOYAKU_SETTING_NAME, '{}'), true);
    }

    /**
     * 任意の設定を取得
     * @param string $key
     * @return string|array|null
     */
    public function get($key)
    {
        return $this->settings_cache[$key] ?? null;
    }

    /**
     * 任意の設定値を更新する
     * @param $key
     * @param $value
     * @return bool True if the value was updated, false otherwise.
     */
    public function set($key, $value)
    {
        if (is_string($value)) {
            $value = trim($value);
        }
        $this->settings_cache[$key] = $value;
        return $this->update_wp_option();

    }

    /**
     * 全ての設定を取得
     * @return mixed
     */
    public function get_all_settings()
    {
        return $this->settings_cache;
    }

    /**
     * 全設定を更新する
     * @param array $settings key-value のリスト
     * @return void
     */
    public function set_all_settings($settings)
    {
        $this->settings_cache = $settings;
        $this->update_wp_option();
    }

    /**
     * 現在の設定をwpに保存する
     * @return bool True if the value was updated, false otherwise.
     */
    private function update_wp_option()
    {
        return update_option(YOYAKU_SETTING_NAME, wp_json_encode($this->settings_cache));
    }

    /**
     * @return array
     */
    public function get_bcc_emails()
    {
        $bcc_email = $this->get('bcc_email');
        return ($bcc_email !== '') ? explode(',', $bcc_email) : [];
    }

    /**
     * @return bool
     */
    public function google_is_active()
    {
        return $this->get('google_client_id')
            && $this->get('google_client_secret');
    }

    /**
     * @return bool
     */
    public function zoom_is_active()
    {
        return $this->get('zoom_account_id')
            && $this->get('zoom_client_id')
            && $this->get('zoom_client_secret');
    }

    /**
     * Stripeのパブリックキーを返す。
     * testモードの時はテストキーを、liveモードの時はライブキーを返す。
     * 無効化されている場合や、未設定の場合は空文字を返す。
     * フロント用メソッド
     * @return string
     */
    public function get_stripe_publishable_key()
    {
        if ($this->get('stripe_test_mode')) {
            return $this->get('stripe_test_publishable_key');
        } else {
            return $this->get('stripe_live_publishable_key');
        }
    }

    public function get_stripe_secret_key()
    {
        if ($this->get('stripe_test_mode')) {
            return $this->get('stripe_test_secret_key');
        } else {
            return $this->get('stripe_live_secret_key');
        }
    }

    /**
     * stripeが利用可能か否か
     * @return bool
     */
    public function stripe_is_active()
    {
        return $this->get('currency') && $this->get_stripe_publishable_key() && $this->get_stripe_secret_key();
    }

    /**
     * 一般設定で設定されているタイムゾーンの時差を返す
     * @return string -09:00 や +01:30 形式の文字列を返す
     */
    public function get_timezone_offset()
    {
        $offset = (float)get_option('gmt_offset');
        $hours = (int)$offset;
        $minutes = ($offset - $hours);

        $sign = ($offset < 0) ? '-' : '+';
        $abs_hour = abs($hours);
        $abs_mins = abs($minutes * 60);
        $tz_offset = sprintf('%s%02d:%02d', $sign, $abs_hour, $abs_mins);

        return $tz_offset;
    }

    /**
     * フロントエンドで使う設定を取得
     * @return array
     * @throws Exception
     */
    public function get_settings_for_front()
    {
        $setting_keys = [
            'default_country_code',
            'terms_of_service_url',
            'phone_field_status',
            'ruby_field_status',
            'birthday_field_status',
            'address_field_status',
            'zipcode_field_status',
            'gender_field_status',
            'google_recaptcha_site_key',
            'google_recaptcha_secret_key',
            'currency',
            'symbol',
            'price_symbol_position',
            'price_decimals',
            'price_thousand_separator',
            'price_decimal_separator',
        ];

        $result = [];
        foreach ($setting_keys as $key) {
            $result[$key] = $this->get($key);
        }
        $result['stripe_publishable_key'] = $this->get_stripe_publishable_key();

        return $result;
    }

    /**
     * 管理者ページで閲覧できる設定
     * @return array
     * @throws Exception
     */
    public function get_settings_for_admin()
    {
        $result = $this->get_settings_for_front();
        if (!is_admin()) {
            return $result;
        }

        // 管理ページの場合
        $current_screen_id = get_current_screen()->id;
        $current_screen = substr($current_screen_id, strrpos($current_screen_id, '-') + 1);
        $capability = [
            'can_read' => current_user_can('yoyaku_read_' . $current_screen),
            'can_write' => current_user_can('yoyaku_write_' . $current_screen),
            'can_delete' => current_user_can('yoyaku_delete_' . $current_screen),
        ];

        $result['capability'] = $capability;
        $result['current_user_id'] = get_current_user_id();
        $result['zoom_is_active'] = $this->zoom_is_active();
        $result['google_calendar'] = $this->get('google_client_id') && $this->get('google_client_secret');
        $result['yoyaku_is_activated'] = 'valid' === $this->get('yoyaku_license_status');

        return $result;
    }

}

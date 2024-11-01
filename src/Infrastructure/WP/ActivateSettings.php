<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\WP;

use Yoyaku\Domain\Setting\OptionFieldStatus;

/**
 * アクティベーションやアップデートの時に設定値を登録する
 */
class ActivateSettings
{
    /**
     * 設定値を登録　既に設定値が存在する場合は上書きしない
     */
    public static function activate()
    {
        $default_settings = [
            // activation
            'yoyaku_license_key' => '',
            'yoyaku_license_status' => 'invalid', // 内部利用
            'yoyaku_license_expires' => '', // 内部利用
            'yoyaku_check_license_timeout' => time(), // 内部利用

            // general
            'default_country_code' => '', // 英語2文字の国コード (例 JP, US)
            'terms_of_service_url' => '', // 利用規約url
            'phone_field_status' => OptionFieldStatus::HIDDEN->value,
            'ruby_field_status' => OptionFieldStatus::HIDDEN->value,
            'birthday_field_status' => OptionFieldStatus::HIDDEN->value,
            'zipcode_field_status' => OptionFieldStatus::HIDDEN->value,
            'address_field_status' => OptionFieldStatus::HIDDEN->value,
            'gender_field_status' => OptionFieldStatus::HIDDEN->value,
            'delete_content' => false,


            // notification
            'sender_name' => '',
            'sender_email' => '',
            'bcc_email' => '', // カンマ区切りの複数のメールアドレス
            'cancel_url' => '',
            'email_service' => 'wp_mail', // wp_mail, smtp
            'smtp_host' => '',
            'smtp_port' => null,
            'smtp_secure' => 'ssl', // ssl, tls
            'smtp_username' => '',
            'smtp_password' => '',

            // payment
            'symbol' => '¥', // 通貨記号
            'price_symbol_position' => 'before', // symbolの表示場所 before, after
            'price_decimals' => 0, // 小数点以下の桁数
            'price_thousand_separator' => ',', // 千桁の区切り文字
            'price_decimal_separator' => '.', // 小数点の区切り文字

            // stripe
            'currency' => '', // 通貨 3文字のISOコード paypalでも使う想定
            'stripe_test_mode' => false,
            'stripe_live_secret_key' => '',
            'stripe_live_publishable_key' => '',
            'stripe_test_secret_key' => '',
            'stripe_test_publishable_key' => '',

            // google recaptcha
            'google_recaptcha_site_key' => '',
            'google_recaptcha_secret_key' => '',

            // google calendar & meet
            'google_enable_google_meet' => false, // カレンダーの予定にgoogle meetも追加するならtrue
            'google_client_id' => '',
            'google_client_secret' => '',
            'google_redirect_uri' => get_site_url() . '/wp-admin/admin.php?page=yoyaku-workers',

            // zoom
            'zoom_account_id' => '',
            'zoom_client_id' => '',
            'zoom_client_secret' => '',
        ];

        $current_settings = json_decode(get_option(YOYAKU_SETTING_NAME, '{}'), true);
        $updated = wp_json_encode(array_merge($default_settings, $current_settings));
        update_option(YOYAKU_SETTING_NAME, $updated);
    }
}

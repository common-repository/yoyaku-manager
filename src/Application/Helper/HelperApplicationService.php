<?php declare(strict_types=1);

namespace Yoyaku\Application\Helper;

use Yoyaku\Domain\Setting\SettingsService;

class HelperApplicationService
{
    /**
     * Returns formatted price based on price plugin settings
     * @param int|float $price
     * @return string
     */
    public static function get_formatted_price($price)
    {
        $settings = SettingsService::get_instance();

        $price_symbol_position = $settings->get('price_symbol_position');
        $symbol = $settings->get('symbol');
        $price_decimals = intval($settings->get('price_decimals'));

        // 価格の区切り文字
        $thousand_separator = $settings->get('price_thousand_separator');
        $decimal_separator = $settings->get('price_decimal_separator');

        // 価格 接頭辞
        $price_prefix = '';
        if ($price_symbol_position === 'before') {
            $price_prefix = $symbol . ' ';
        }

        // 価格 接尾辞
        $price_suffix = '';
        if ($price_symbol_position === 'after') {
            $price_suffix = ' ' . $symbol;
        }

        $formatted_number = number_format($price, $price_decimals, $decimal_separator, $thousand_separator);
        return $price_prefix . $formatted_number . $price_suffix;
    }

    /**
     * 秒数から時分の文字列を返す。 1分未満は切り捨てる。
     * @param int $minutes
     * @return string
     */
    public static function minutes_to_nice_duration($minutes)
    {
        $hours = floor($minutes / 60);
        $minutes_ = $minutes % 60;
        return ($hours ? ($hours . 'h') : '') . ($hours && $minutes_ ? ' ' : '') . ($minutes_ ? ($minutes_ . 'min') : '');
    }

    /**
     * @return bool pro版ならtrue
     */
    public static function is_pro()
    {
        return str_ends_with(YOYAKU_PATH, 'pro');
    }
}

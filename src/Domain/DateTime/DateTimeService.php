<?php declare(strict_types=1);

namespace Yoyaku\Domain\DateTime;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

/**
 *
 */
class DateTimeService
{
    private static ?DateTimeZone $timezone = null;

    /**
     * @return DateTimeZone
     * @throws Exception
     */
    public static function get_timezone()
    {
        if (!self::$timezone) {
            // wp_timezone_string()は、一般設定 > タイムゾーン の設定に基づいたタイムゾーンを取得する
            // '東京'の場合は'Asia/Tokyo'、'UTC-9:30'の場合は'-09:30' を返す
            self::set_timezone(wp_timezone_string());
        }
        return self::$timezone;
    }

    /**
     * @param string $timezone wp_timezone_string()の戻り値
     * @throws Exception
     */
    public static function set_timezone($timezone)
    {
        self::$timezone = new DateTimeZone($timezone);
    }

    /**
     * タイムゾーン設定に応じた現在のDateTimeオブジェクトを取得
     * @return DateTimeImmutable
     * @throws Exception
     */
    public static function get_now_datetime_object()
    {
        return self::get_custom_datetime_object('now');
    }

    /**
     * タイムゾーン設定に応じた現在の日時の文字列を取得
     * @return string Y-m-d H:i:s 形式
     */
    public static function get_now_datetime()
    {
        return current_time('Y-m-d H:i:s');
    }

    /**
     * 指定した日時のDateTimeオブジェクトを取得
     * @param String $datetime_string
     * @return DateTimeImmutable
     * @throws Exception
     */
    public static function get_custom_datetime_object($datetime_string)
    {
        return new DateTimeImmutable($datetime_string, self::get_timezone());
    }

    /**
     * Return custom date and time string by timezone settings
     * @param String $datetime_string
     * @return string
     * @throws Exception
     */
    public static function get_custom_datetime($datetime_string)
    {
        return self::get_custom_datetime_object($datetime_string)->format('Y-m-d H:i:s');
    }

    /**
     * UTCの現在の日時を表すDateTimeオブジェクトを取得
     * @return DateTimeImmutable
     * @throws Exception
     */
    public static function get_now_datetime_object_in_utc()
    {
        return self::get_now_datetime_object()->setTimezone(new DateTimeZone('UTC'));
    }

    /**
     * UTCの現在の日時を表す文字列を取得
     * @return string
     */
    public static function get_now_datetime_in_utc()
    {
        return current_time('Y-m-d H:i:s', true);
    }

    /**
     * ユーザーが設定したTimeZoneの日時を、$timezoneのタイムゾーンに変換したDateTimeオブジェクトを取得
     * @param $datetime_string
     * @param $timezone
     * @return DateTimeImmutable
     * @throws Exception
     */
    public static function get_custom_datetime_object_in_time_zone($datetime_string, $timezone)
    {
        return self::get_custom_datetime_object($datetime_string)->setTimezone(new DateTimeZone($timezone));
    }

    /**
     * ユーザーが設定したTimeZoneの日時を、UTC時間に変換したDateTimeオブジェクトを取得
     * @param $datetime_string
     * @return DateTimeImmutable
     * @throws Exception
     */
    public static function get_custom_datetime_object_in_utc($datetime_string)
    {
        return self::get_custom_datetime_object_in_time_zone($datetime_string, 'UTC');
    }

    /**
     * ユーザーが設定したTimeZoneの日時を、UTC時間に変換した文字列を取得
     * @param $datetime_string
     * @return string
     * @throws Exception
     */
    public static function get_custom_datetime_in_utc($datetime_string)
    {
        return self::get_custom_datetime_object_in_utc($datetime_string)->format('Y-m-d H:i:s');
    }

    /**
     * UTCからユーザーが設定したTimeZoneのDateTimeオブジェクトを返す
     * @param $datetime_string
     * @return DateTimeImmutable
     * @throws Exception
     */
    public static function get_custom_datetime_object_from_utc($datetime_string)
    {
        return (new DateTimeImmutable($datetime_string, new DateTimeZone('UTC')))->setTimezone(self::get_timezone());
    }

    /**
     * タイムゾーン設定に応じた本日の日付の文字列を取得
     * @return string
     */
    public static function get_now_date()
    {
        return current_time('Y-m-d');
    }

    /**
     * 日時が早い順にソートする
     * @param array $datetime_list
     * @return array
     */
    public static function get_sorted_datetime_strings($datetime_list)
    {
        usort($datetime_list, fn($a, $b) => strtotime($a) - strtotime($b));
        return $datetime_list;
    }
}

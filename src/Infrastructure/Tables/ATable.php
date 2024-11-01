<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Tables;


/**
 * データベースのテーブルの抽象クラス
 */
abstract class ATable
{
    // テーブル名やキー名は65文字以上はエラーになるため、長いテーブル名には気を付ける。
    const TABLE = '';

    /**
     * テーブルを作成する
     */
    public static function init()
    {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        static::build_table();
    }

    /**
     * @return string
     */
    abstract public static function build_table();

    /**
     * テーブルを削除する
     */
    public static function drop()
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $wpdb->query("DROP TABLE IF EXISTS {$table_name};");
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        global $wpdb;
        return $wpdb->prefix . 'yoyaku_' . static::TABLE;
    }
}

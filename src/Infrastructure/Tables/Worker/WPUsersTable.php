<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Tables\Worker;

use Yoyaku\Infrastructure\Tables\ATable;

/**
 * wpのユーザーテーブルクラス
 */
class WPUsersTable extends ATable
{
    const TABLE = 'users';
    const META_TABLE = 'usermeta';

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return self::get_database_base_prefix() . static::TABLE;
    }

    /**
     * @return string
     */
    public static function get_database_base_prefix()
    {
        global $wpdb;
        //$base_prefix: wp-config.php で定義されている元のプレフィックス。
        // マルチサイトの場合: ブログ番号を追加せずにプレフィックスを取得したい場合に使用する
        return $wpdb->base_prefix;
    }

    public static function build_table()
    {
        return "";
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Tables\Migration;

use mysqli_result;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Infrastructure\Tables\ATable;

/**
 * DBのマイグレーションを管理するテーブル
 */
class MigrationsTable extends ATable
{
    const TABLE = 'migrations';

    /**
     * @return bool|int|mysqli_result|null
     */
    public static function build_table()
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $name = Name::MAX_LENGTH;

        return $wpdb->query(
            $wpdb->prepare("
                CREATE TABLE IF NOT EXISTS $table_name (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `name` varchar (%d) NOT NULL,
                    `created` DATETIME NOT NULL,
                    PRIMARY KEY (`id`)
                )
                DEFAULT CHARSET=utf8 COLLATE utf8_general_ci",
                [$name]
            )
        );
    }

    /**
     * 初期データをテーブルに登録する
     * @return bool
     */
    public static function add_initial_rows()
    {
        global $wpdb;
        $table_name = static::get_table_name();

        // 登録済みの場合は何もしない
        if (0 !== intval($wpdb->get_var("SELECT COUNT(*) FROM $table_name"))) {
            return true;
        }

        return boolval(
            $wpdb->insert($table_name, ['name' => '1.0.0', 'created' => current_time('Y-m-d H:i:s', true)])
        );
    }
}

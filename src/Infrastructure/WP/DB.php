<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\WP;

use Yoyaku\Application\Common\Exceptions\WpDbException;

/**
 * $wpdbのメソッドのラッパー + トランザクション用メソッドを管理するクラス
 */
class DB
{
    /**
     * @return void
     */
    public static function begin()
    {
        global $wpdb;
        $wpdb->query("BEGIN");
    }

    /**
     * @return void
     */
    public static function commit()
    {
        global $wpdb;
        $wpdb->query("COMMIT");
    }

    /**
     * @return void
     */
    public static function rollback()
    {
        global $wpdb;
        $wpdb->query("ROLLBACK");
    }

    /**
     * @param string $table
     * @param array $where
     * @param string[]|string $where_format
     * @return int|false The number of rows deleted, or false on error.
     * @throws WpDbException
     */
    public function delete($table, $where, $where_format = null)
    {
        global $wpdb;
        $result = $wpdb->delete($table, $where, $where_format);
        if ($result === false) {
            throw new WpDbException(__('Delete data error.', 'yoyaku-manager'));
        }
        return $result;
    }

    /**
     * @param string $table
     * @param array $data
     * @param string[]|string $format
     * @return int|false The number of rows inserted, or false on error.
     * @throws WpDbException
     */
    public function insert($table, $data, $format = null)
    {
        global $wpdb;
        $result = $wpdb->insert($table, $data, $format);
        if ($result === false) {
            throw new WpDbException(__('Add data error.', 'yoyaku-manager'));
        }
        return $result;
    }

    /**
     * @return int
     */
    public function last_insert_id()
    {
        global $wpdb;
        return $wpdb->insert_id;
    }

    /**
     * データを更新する。
     * $dataがデータベース内に既に存在するものと一致する場合、行は更新されないため、0が返されることに注意。
     * このため、false === $result のようにして戻り値を確認する必要がある。
     * @param string $table
     * @param array $data
     * @param array $where
     * @param string[]|string $format
     * @param string[]|string $where_format
     * @return int The number of rows affected if successful.
     * @throws WpDbException
     */
    public function update($table, $data, $where, $format = null, $where_format = null)
    {
        global $wpdb;
        $result = $wpdb->update($table, $data, $where, $format, $where_format);
        if ($result === false) {
            throw new WpDbException(esc_html__('Update data error.', 'yoyaku-manager'));
        }
        return $result;
    }
}

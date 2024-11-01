<?php declare(strict_types=1);

namespace Yoyaku\Application\Routes\Common;

/**
 * register_rest_route()のvalidate_callback()で使うバリデーション関数
 */
class Validation
{
    /**
     * Json形式チェック
     * @param $param
     * @param $request
     * @param $key
     * @return bool Json形式ならTrue
     */
    public static function validate_json($param, $request, $key)
    {
        return !is_null(json_decode($param));
    }

    /**
     * Y-m-d形式かチェックする
     * @param $param
     * @param $request
     * @param $key
     * @return bool
     */
    public static function validate_date($param, $request, $key)
    {
        list($y, $m, $d) = explode('-', $param);
        return checkdate((int)$m, (int)$d, (int)$y);
    }
}


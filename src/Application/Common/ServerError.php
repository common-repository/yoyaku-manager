<?php declare(strict_types=1);

namespace Yoyaku\Application\Common;

use WP_Error;

/**
 * サーバーエラー（主にDB接続エラー）が発生した時に返すステータスコードが500のレスポンス
 */
class ServerError extends WP_Error
{
    public function __construct($code, $message)
    {
        // 第3引数に 'status' がキーの要素を設定すると、レスポンスのステータスコードになる
        parent::__construct($code, $message, ['status' => 500]);
    }
}

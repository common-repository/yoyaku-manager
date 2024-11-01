<?php declare(strict_types=1);

namespace Yoyaku\Application\Common;

use Yoyaku\Infrastructure\WP\DB;

/**
 * クエリサービスの抽象クラス
 */
class AQuery
{
    protected DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }
}

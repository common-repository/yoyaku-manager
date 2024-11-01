<?php declare(strict_types=1);

namespace Yoyaku\Application\Routes\Common;

class Patterns
{
    /** @var string 空文字 or 電話番号のみ 先頭の+やハイフンは利用可能 */
    public static $phone = '^(|\+?[0-9-]+)$';
    public static $time = '^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$';

}


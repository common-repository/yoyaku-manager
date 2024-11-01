<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject\Enum;


/**
 * 表示状態を表すクラス
 */
enum Status: string
{
    case VISIBLE = 'visible';
    case DISABLED = 'disabled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
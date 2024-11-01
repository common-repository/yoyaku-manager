<?php declare(strict_types=1);

namespace Yoyaku\Domain\Setting;


/**
 * カスタムできる個人情報のフォームの設定値クラス
 */
enum OptionFieldStatus: string
{
    case HIDDEN = 'hidden';
    case OPTIONAL = 'optional';
    case REQUIRED = 'required';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
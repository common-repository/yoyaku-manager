<?php declare(strict_types=1);

namespace Yoyaku\Domain\Customer;

/**
 * 性別 値オブジェクト
 */
enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case UNKNOWN = '';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::MALE => __('Male', 'yoyaku-manager'),
            self::FEMALE => __('Female', 'yoyaku-manager'),
            self::UNKNOWN => "",
        };
    }
}

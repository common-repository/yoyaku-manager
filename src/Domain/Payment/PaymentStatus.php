<?php declare(strict_types=1);

namespace Yoyaku\Domain\Payment;

/**
 * PaymentStatus 値オブジェクト
 */
enum PaymentStatus: string
{
    case PAID = 'paid'; // 有料/支払い済
    case PENDING = 'pending'; // 承認待ち
    case REFUNDED = 'refunded'; // 返金

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::PAID => __('Paid', 'yoyaku-manager'),
            self::PENDING => __('Pending', 'yoyaku-manager'),
            self::REFUNDED => __('Refunded', 'yoyaku-manager'),
        };
    }
}

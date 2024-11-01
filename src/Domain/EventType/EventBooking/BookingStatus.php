<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\EventBooking;

/**
 * BookingStatus 値オブジェクト
 */
enum BookingStatus: string
{
    case APPROVED = 'approved'; // 承認
    case PENDING = 'pending'; // 保留中
    case CANCELED = 'canceled'; // キャンセル
    case DISAPPROVED = 'disapproved'; // 不承認

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::APPROVED => __('Approved', 'yoyaku-manager'),
            self::PENDING => __('Pending', 'yoyaku-manager'),
            self::CANCELED => __('Canceled', 'yoyaku-manager'),
            self::DISAPPROVED => __('Disapproved', 'yoyaku-manager'),
        };
    }
}
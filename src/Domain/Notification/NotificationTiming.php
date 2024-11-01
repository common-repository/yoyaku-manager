<?php declare(strict_types=1);

namespace Yoyaku\Domain\Notification;

/**
 * NotificationTiming 値オブジェクト
 */
enum NotificationTiming: string
{
    case APPROVED = 'approved';
    case PENDING = 'pending';
    case CANCELED = 'canceled';
    case DISAPPROVED = 'disapproved';
    case SCHEDULED = 'scheduled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
<?php declare(strict_types=1);

namespace Yoyaku\Domain\Payment;

/**
 * 支払いタイプのクラス 値オブジェクト
 */
enum GatewayType: string
{
    case ON_SITE = 'on_site';
    case STRIPE = 'stripe';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::ON_SITE => __('On Site', 'yoyaku-manager'),
            self::STRIPE => __('Stripe', 'yoyaku-manager'),
        };
    }
}

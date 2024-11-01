<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Services\Payment;

use Yoyaku\Domain\Payment\GatewayType;

/**
 * Class PaymentFactory
 */
class PaymentGatewayFactory
{
    /**
     * @param GatewayType $gateway
     * @return APaymentGateway
     */
    public static function create(GatewayType $gateway)
    {
        switch ($gateway) {
            case GatewayType::ON_SITE:
                return new OnSiteService();
        }
    }
}

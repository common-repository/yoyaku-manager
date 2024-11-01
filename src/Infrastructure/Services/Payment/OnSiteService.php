<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Services\Payment;

use Exception;
use Yoyaku\Domain\DateTime\DateTimeService;
use Yoyaku\Domain\Payment\PurchaseResult;
use Yoyaku\Domain\ValueObject\DateTime\DateTimeValue;
use Yoyaku\Domain\ValueObject\String\Name;

class OnSiteService extends APaymentGateway
{
    /**
     * @param $options
     * @return PurchaseResult
     * @throws Exception
     */
    public function purchase($options)
    {
        return new PurchaseResult(
            new Name(''),
            new DateTimeValue(DateTimeService::get_now_datetime_object())
        );
    }
}

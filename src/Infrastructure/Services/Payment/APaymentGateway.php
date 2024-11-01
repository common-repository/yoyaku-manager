<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Services\Payment;

use Yoyaku\Domain\Payment\PurchaseResult;

/**
 * 支払い方法のインターフェースの共通化 omnipay参照
 * https://github.com/thephpleague/omnipay?tab=readme-ov-file
 */
abstract class APaymentGateway
{
    /**
     * 決済
     * @param array $options
     * @return PurchaseResult
     */
    abstract public function purchase($options);
}
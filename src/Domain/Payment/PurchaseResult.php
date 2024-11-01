<?php declare(strict_types=1);

namespace Yoyaku\Domain\Payment;

use Yoyaku\Domain\ValueObject\DateTime\DateTimeValue;
use Yoyaku\Domain\ValueObject\String\Name;

/**
 * PaymentResult 値オブジェクト
 */
final class PurchaseResult
{
    private Name $transaction_id;
    /**
     * transactionが作成された日時. タイムゾーンはwp設定のタイムゾーン
     * @var DateTimeValue
     */
    private DateTimeValue $created;

    /**
     * @param Name $transaction_id
     * @param DateTimeValue $created
     */
    public function __construct($transaction_id, $created)
    {
        $this->transaction_id = $transaction_id;
        $this->created = $created;
    }

    /**
     * @return Name
     */
    public function get_transaction_id()
    {
        return $this->transaction_id;
    }

    /**
     * @return DateTimeValue
     */
    public function get_created()
    {
        return $this->created;
    }
}

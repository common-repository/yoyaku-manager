<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\EventTicket;

use Yoyaku\Domain\Collection\Collection;

/**
 * BuyEventTicketクラス用のコレクションクラス
 */
class BuyEventTicketCollection extends Collection
{
    /**
     * 購入金額を取得
     * @return float|int
     */
    function get_amount()
    {
        $amount = 0.0;
        foreach ($this->get_items() as $buy_ticket) {
            $amount += $buy_ticket->get_price()->get_value() * $buy_ticket->get_buy_count()->get_value();
        }
        return $amount;
    }
}

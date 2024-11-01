<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\EventTicket;

use Yoyaku\Domain\Common\AFactory;
use Yoyaku\Domain\ValueObject\Number\Count;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\Number\Price;
use Yoyaku\Domain\ValueObject\String\Name;

class BuyEventTicketFactory extends AFactory
{
    /**
     * @param array $fields
     * @return BuyEventTicket
     */
    public static function create($fields)
    {
        $buy_event_ticket = new BuyEventTicket(
            new Id($fields['id']),
            new Count($fields['buy_count']),
        );

        if (isset($fields['name'])) {
            $buy_event_ticket->set_name(new Name($fields['name']));
        }

        if (isset($fields['ticket_count'])) {
            $buy_event_ticket->set_ticket_count(new Count($fields['ticket_count']));
        }

        if (isset($fields['price'])) {
            $buy_event_ticket->set_price(new Price((float)$fields['price']));
        }

        return $buy_event_ticket;
    }
}

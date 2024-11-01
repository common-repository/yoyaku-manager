<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\EventTicket;

use Yoyaku\Domain\Common\AFactory;
use Yoyaku\Domain\ValueObject\Number\Count;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\Number\Price;
use Yoyaku\Domain\ValueObject\String\Name;

class EventTicketFactory extends AFactory
{
    /**
     * @param array $fields
     * @return EventTicket
     */
    public static function create($fields)
    {
        $event_ticket = new EventTicket(
            new Id($fields['event_id']),
            new Name($fields['name']),
            new Count($fields['ticket_count']),
            new Price((float)$fields['price']),
        );

        if (isset($fields['id'])) {
            $event_ticket->set_id(new Id($fields['id']));
        }

        if (isset($fields['sold_ticket_count'])) {
            $event_ticket->set_sold_ticket_count(new Count($fields['sold_ticket_count']));
        }

        return $event_ticket;
    }
}

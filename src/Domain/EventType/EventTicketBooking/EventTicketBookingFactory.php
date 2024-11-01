<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\EventTicketBooking;

use InvalidArgumentException;
use Yoyaku\Domain\Common\AFactory;
use Yoyaku\Domain\ValueObject\Number\Count;
use Yoyaku\Domain\ValueObject\Number\Id;

class EventTicketBookingFactory extends AFactory
{
    /**
     * @param array $fields
     * @return EventTicketBooking
     * @throws InvalidArgumentException
     */
    public static function create($fields)
    {
        $ticket = new EventTicketBooking(
            new Id($fields['event_booking_id']),
            new Id($fields['event_ticket_id']),
            new Count($fields['buy_count']),
        );

        if (!empty($fields['id'])) {
            $ticket->set_id(new Id($fields['id']));
        }

        return $ticket;
    }
}

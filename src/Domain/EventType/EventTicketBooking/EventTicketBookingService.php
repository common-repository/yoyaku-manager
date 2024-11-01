<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\EventTicketBooking;

use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\Common\AEntityService;
use Yoyaku\Domain\EventType\EventTicket\BuyEventTicket;
use Yoyaku\Domain\EventType\EventTicket\BuyEventTicketCollection;
use Yoyaku\Infrastructure\Repository\EventType\EventTicketBookingRepository;


class EventTicketBookingService extends AEntityService
{
    /**
     * @param EventTicketBookingRepository $repo
     */
    public function __construct(EventTicketBookingRepository $repo)
    {
        parent::__construct($repo, EventTicketBookingFactory::class);
    }

    /**
     * チケット購入履歴を一括追加
     * @param $event_booking_id
     * @param BuyEventTicketCollection<BuyEventTicket> $buy_tickets
     * @return int
     */
    public function bulk_add($event_booking_id, $buy_tickets)
    {
        $add_entities = [];
        foreach ($buy_tickets->get_items() as $ticket) {
            if (0 == $ticket->get_buy_count()->get_value()) {
                continue;
            }
            $add_entities[] = EventTicketBookingFactory::create([
                'event_booking_id' => $event_booking_id,
                'event_ticket_id' => $ticket->get_id()->get_value(),
                'buy_count' => $ticket->get_buy_count()->get_value(),
            ]);
        }

        return $this->repo->bulk_add(new Collection($add_entities));
    }
}

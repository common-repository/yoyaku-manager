<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\EventTicket;

use Yoyaku\Domain\Common\AEntityService;
use Yoyaku\Infrastructure\Repository\EventType\EventTicketRepository;

class EventTicketService extends AEntityService
{
    public function __construct(EventTicketRepository $repo)
    {
        parent::__construct($repo, EventTicketFactory::class);
    }
}

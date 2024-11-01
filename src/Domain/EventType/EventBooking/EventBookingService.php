<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\EventBooking;

use Yoyaku\Domain\Common\AEntityService;
use Yoyaku\Infrastructure\Repository\EventType\EventBookingRepository;

class EventBookingService extends AEntityService
{
    public function __construct(EventBookingRepository $repo)
    {
        parent::__construct($repo, EventBookingFactory::class);
    }
}

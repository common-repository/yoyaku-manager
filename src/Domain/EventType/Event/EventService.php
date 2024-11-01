<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\Event;

use Yoyaku\Domain\Common\AEntityService;
use Yoyaku\Infrastructure\Repository\EventType\EventRepository;

class EventService extends AEntityService
{
    public function __construct(EventRepository $repo)
    {
        parent::__construct($repo, EventFactory::class);
    }
}

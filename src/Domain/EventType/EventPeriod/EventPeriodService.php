<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\EventPeriod;

use Yoyaku\Domain\Common\AEntityService;
use Yoyaku\Infrastructure\Repository\EventType\EventPeriodRepository;

class EventPeriodService extends AEntityService
{
    public function __construct(EventPeriodRepository $repo)
    {
        parent::__construct($repo, EventPeriodFactory::class);
    }
}

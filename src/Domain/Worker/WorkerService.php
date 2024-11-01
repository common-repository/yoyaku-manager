<?php declare(strict_types=1);

namespace Yoyaku\Domain\Worker;

use Yoyaku\Domain\Common\AEntityService;
use Yoyaku\Infrastructure\Repository\Worker\WorkerRepository;

/**
 * Class WorkerService
 */
class WorkerService extends AEntityService
{
    /**
     * @param WorkerRepository $repo
     */
    public function __construct(WorkerRepository $repo)
    {
        parent::__construct($repo, WorkerFactory::class);
    }
}

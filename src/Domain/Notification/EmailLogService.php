<?php declare(strict_types=1);

namespace Yoyaku\Domain\Notification;

use Yoyaku\Domain\Common\AEntityService;
use Yoyaku\Infrastructure\Repository\Notification\EmailLogRepository;

class EmailLogService extends AEntityService
{
    public function __construct(EmailLogRepository $repo)
    {
        parent::__construct($repo, EmailLogFactory::class);
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Domain\Notification;

use Yoyaku\Domain\Common\AEntityService;
use Yoyaku\Infrastructure\Repository\Notification\NotificationRepository;

class NotificationService extends AEntityService
{
    public function __construct(NotificationRepository $repo)
    {
        parent::__construct($repo, NotificationFactory::class);
    }
}

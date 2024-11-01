<?php declare(strict_types=1);

namespace Yoyaku\Domain\Notification;

use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\Common\AEntityService;
use Yoyaku\Infrastructure\Repository\Notification\NotificationEventRepository;

class NotificationEventService extends AEntityService
{
    public function __construct(NotificationEventRepository $repo)
    {
        parent::__construct($repo, NotificationEventFactory::class);
    }

    /**
     * @param $event_id
     * @param $notification_ids
     * @return int
     */
    public function bulk_add($event_id, $notification_ids)
    {
        $add_entities = [];
        foreach ($notification_ids as $notification_id) {
            $add_entities[] = NotificationEventFactory::create([
                'event_id' => $event_id,
                'notification_id' => $notification_id,
            ]);
        }
        return $this->repo->bulk_add(new Collection($add_entities));
    }
}

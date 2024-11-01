<?php declare(strict_types=1);

namespace Yoyaku\Application\EventType\Event;

use InvalidArgumentException;
use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Domain\EventType\Event\EventFactory;
use Yoyaku\Domain\EventType\Event\EventService;
use Yoyaku\Infrastructure\Repository\EventType\EventRepository;
use Yoyaku\Infrastructure\Repository\Notification\NotificationRepository;

class EventApplicationService
{
    private EventService $event_ds;
    private EventRepository $event_repo;
    private NotificationRepository $notification_repo;

    /**
     * @param EventService $event_ds
     * @param EventRepository $event_repo
     * @param NotificationRepository $notification_repo
     */
    public function __construct(
        EventService           $event_ds,
        EventRepository        $event_repo,
        NotificationRepository $notification_repo,
    )
    {
        $this->event_ds = $event_ds;
        $this->event_repo = $event_repo;
        $this->notification_repo = $notification_repo;
    }

    /**
     * イベントを更新する
     * @throws DataNotFoundException
     * @throws InvalidArgumentException
     * @throws WpDbException
     */
    public function update($id, $update_fields)
    {
        $event_period = $this->event_repo->get_by_id($id);
        $new_event_period = EventFactory::create(array_merge($event_period->to_array(), $update_fields));
        return $this->event_repo->update_by_entity($id, $new_event_period);
    }
}

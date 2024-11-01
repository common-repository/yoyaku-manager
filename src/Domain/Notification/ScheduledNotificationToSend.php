<?php declare(strict_types=1);

namespace Yoyaku\Domain\Notification;

use Yoyaku\Domain\EventType\Event\Event;
use Yoyaku\Domain\EventType\EventPeriod\EventPeriod;
use Yoyaku\Domain\ValueObject\Number\Id;

/**
 * 定期通知に必要なデータセット 値オブジェクト
 */
class ScheduledNotificationToSend
{
    private Event $event;
    private EventPeriod $event_period;
    private Notification $notification;

    /**
     *
     */
    public function __construct($notification, $event, $event_period)
    {
        $this->notification = $notification;
        $this->event = $event;
        $this->event_period = $event_period;
    }

    /**
     * @return Notification
     */
    public function get_notification()
    {
        return $this->notification;
    }

    /**
     * @return Event
     */
    public function get_event()
    {
        return $this->event;
    }

    /**
     * @return EventPeriod
     */
    public function get_event_period()
    {
        return $this->event_period;
    }

    /**
     * @return Id
     */
    public function get_notification_id()
    {
        return $this->notification->get_id();
    }

    /**
     * @return Id
     */
    public function get_event_period_id()
    {
        return $this->event_period->get_id();
    }
}

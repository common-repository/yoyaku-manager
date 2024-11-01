<?php declare(strict_types=1);

namespace Yoyaku\Domain\Notification;

use Yoyaku\Domain\Common\AFactory;
use Yoyaku\Domain\ValueObject\Number\Id;

class NotificationEventFactory extends AFactory
{
    /**
     * @param array $fields
     * @return NotificationEvent
     */
    public static function create($fields)
    {
        $notification_event = new NotificationEvent(
            new Id($fields['event_id']),
            new Id($fields['notification_id']),
        );

        if (isset($fields['id'])) {
            $notification_event->set_id(new Id($fields['id']));
        }

        return $notification_event;
    }
}

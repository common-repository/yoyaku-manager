<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Repository\Notification;

use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\Notification\NotificationEvent;
use Yoyaku\Domain\Notification\NotificationEventFactory;
use Yoyaku\Infrastructure\Repository\ARepository;
use Yoyaku\Infrastructure\Tables\Notification\NotificationsEventsTable;

class NotificationEventRepository extends ARepository
{
    const FACTORY = NotificationEventFactory::class;

    public function __construct()
    {
        $table = NotificationsEventsTable::get_table_name();
        parent::__construct($table);
    }

    /**
     * @param $notification_events Collection<NotificationEvent>
     * @return bool|int
     */
    public function bulk_add($notification_events)
    {
        global $wpdb;

        if (!$notification_events->length()) {
            return 0;
        }

        $query = " INSERT INTO {$this->table} (`event_id`, `notification_id`) VALUES ";
        $insert_values = [];
        /** @var NotificationEvent $notification_event */
        foreach ($notification_events->get_items() as $notification_event) {
            $insert_values[] = $wpdb->prepare("(%d, %d)",
                $notification_event->get_event_id()->get_value(),
                $notification_event->get_notification_id()->get_value(),
            );
        }
        $query .= implode(',', $insert_values);
        return $wpdb->query($query);
    }
}

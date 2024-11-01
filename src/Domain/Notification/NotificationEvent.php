<?php declare(strict_types=1);

namespace Yoyaku\Domain\Notification;

use Yoyaku\Domain\ValueObject\Number\Id;

/**
 * Class NotificationEvent Entity
 */
class NotificationEvent
{
    private ?Id $id = null;
    private Id $event_id;
    private Id $notification_id;

    /**
     * @param Id $event_id
     * @param Id $notification_id
     */
    public function __construct($event_id, $notification_id)
    {
        $this->event_id = $event_id;
        $this->notification_id = $notification_id;
    }

    /**
     * @return Id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @param Id $id
     */
    public function set_id(Id $id)
    {
        $this->id = $id;
    }

    /**
     * @return Id
     */
    public function get_event_id()
    {
        return $this->event_id;
    }

    /**
     * @return Id
     */
    public function get_notification_id()
    {
        return $this->notification_id;
    }

    /**
     * @return array
     */
    public function to_table_data()
    {
        $result = $this->to_array();
        unset($result['id']);
        return $result;
    }

    public function to_array(): array
    {
        return [
            'id' => $this->get_id()?->get_value(),
            'event_id' => $this->get_event_id()->get_value(),
            'notification_id' => $this->get_notification_id()->get_value(),
        ];
    }
}

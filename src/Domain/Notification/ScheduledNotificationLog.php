<?php declare(strict_types=1);

namespace Yoyaku\Domain\Notification;

use Yoyaku\Domain\ValueObject\DateTime\DateTimeValue;
use Yoyaku\Domain\ValueObject\Number\Id;

/**
 * Class NotificationLog
 */
class ScheduledNotificationLog
{
    private ?Id $id = null;
    private Id $notification_id;
    private Id $event_period_id;
    private ?DateTimeValue $created = null;

    /**
     * @param $notification_id
     * @param $event_period_id
     */
    public function __construct($notification_id, $event_period_id)
    {
        $this->notification_id = $notification_id;
        $this->event_period_id = $event_period_id;
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
    public function get_event_period_id()
    {
        return $this->event_period_id;
    }

    /**
     * @return Id
     */
    public function get_notification_id()
    {
        return $this->notification_id;
    }

    /**
     * @return DateTimeValue
     */
    public function get_created()
    {
        return $this->created;
    }

    /**
     * @param DateTimeValue $created
     */
    public function set_created($created)
    {
        $this->created = $created;
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
            'notification_id' => $this->get_notification_id()->get_value(),
            'event_period_id' => $this->get_event_period_id()->get_value(),
            'created' => $this->get_created()?->get_format_value(),
        ];
    }
}

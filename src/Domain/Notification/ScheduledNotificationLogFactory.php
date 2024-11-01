<?php declare(strict_types=1);

namespace Yoyaku\Domain\Notification;

use Exception;
use Yoyaku\Domain\Common\AFactory;
use Yoyaku\Domain\DateTime\DateTimeService;
use Yoyaku\Domain\ValueObject\DateTime\DateTimeValue;
use Yoyaku\Domain\ValueObject\Number\Id;

class ScheduledNotificationLogFactory extends AFactory
{
    /**
     * @param array $fields
     * @return ScheduledNotificationLog
     * @throws Exception
     */
    public static function create($fields)
    {
        $log = new ScheduledNotificationLog(
            new Id($fields['notification_id']),
            new Id($fields['event_period_id']),
        );

        if (isset($fields['id'])) {
            $log->set_id(new Id($fields['id']));
        }

        if (!empty($fields['created'])) {
            $log->set_created(
                new DateTimeValue(DateTimeService::get_custom_datetime_object_from_utc($fields['created']))
            );
        }

        return $log;
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Domain\Notification;

use InvalidArgumentException;
use Yoyaku\Domain\Common\AFactory;
use Yoyaku\Domain\ValueObject\DateTime\Days;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\String\Name;

class NotificationFactory extends AFactory
{
    /**
     * @param array $fields
     * @return Notification
     * @throws InvalidArgumentException
     */
    public static function create($fields)
    {
        $notification = new Notification(
            new Name($fields['name']),
            new Name($fields['subject']),
            new Content($fields['content']),
            NotificationTiming::tryFrom($fields['timing']),
        );

        if (isset($fields['id'])) {
            $notification->set_id(new Id($fields['id']));
        }

        if ($notification->is_scheduled()) {
            if (isset($fields['days']) && !empty($fields['time']) && isset($fields['is_before'])) {
                $notification->set_days(new Days($fields['days']));
                $notification->set_time(new TimeOfDay($fields['time']));
                $notification->set_is_before(boolval($fields['is_before']));
            } else {
                // 定期通知に必要な値が無い場合はエラー
                throw new InvalidArgumentException("Invalid fields. timing or days, time and is_before");
            }
        }

        return $notification;
    }
}

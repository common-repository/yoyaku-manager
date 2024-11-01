<?php declare(strict_types=1);

namespace Yoyaku\Domain\Notification;

use Exception;
use Yoyaku\Domain\Common\AFactory;
use Yoyaku\Domain\DateTime\DateTimeService;
use Yoyaku\Domain\ValueObject\DateTime\DateTimeValue;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\String\Email;
use Yoyaku\Domain\ValueObject\String\Name;

class EmailLogFactory extends AFactory
{
    /**
     * @param array $fields
     * @return EmailLog
     * @throws Exception
     */
    public static function create($fields)
    {
        $email_log = new EmailLog(
            new Id($fields['customer_id']),
            new Email($fields['to']),
            new Name($fields['subject']),
            new Content($fields['content']),
        );

        if (isset($fields['id'])) {
            $email_log->set_id(new Id($fields['id']));
        }

        if (isset($fields['sent_datetime'])) {
            $email_log->set_sent_datetime(
                new DateTimeValue(
                    DateTimeService::get_custom_datetime_object_from_utc($fields['sent_datetime'])
                )
            );
        }

        if (isset($fields['sent'])) {
            $email_log->set_sent((bool)$fields['sent']);
        }

        return $email_log;
    }
}

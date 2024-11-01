<?php declare(strict_types=1);

namespace Yoyaku\Domain\Worker;

use Exception;
use Yoyaku\Domain\Common\AFactory;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\String\Name;

class WorkerFactory extends AFactory
{
    /**
     * @param $fields
     * @return Worker
     * @throws Exception
     */
    public static function create($fields)
    {
        $worker = new Worker(new Id($fields['id']));

        if (!empty($fields['zoom_user_id'])) {
            $worker->set_zoom_user_id(new Name($fields['zoom_user_id']));
        }

        if (!empty($fields['google_calendar_id'])) {
            $worker->set_google_calendar_id(new GoogleCalendarId($fields['google_calendar_id']));
        }

        if (!empty($fields['google_calendar_token'])) {
            $worker->set_google_calendar_token(new AccessToken($fields['google_calendar_token']));
        }

        return $worker;
    }
}

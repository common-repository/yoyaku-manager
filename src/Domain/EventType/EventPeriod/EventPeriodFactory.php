<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\EventPeriod;

use Exception;
use InvalidArgumentException;
use Yoyaku\Domain\Common\AFactory;
use Yoyaku\Domain\DateTime\DateTimeService;
use Yoyaku\Domain\ValueObject\DateTime\DateTimeValue;
use Yoyaku\Domain\ValueObject\Number\Count;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\String\Address;
use Yoyaku\Domain\ValueObject\String\Label;
use Yoyaku\Domain\ValueObject\String\Url;
use Yoyaku\Domain\ValueObject\String\Uuid4;
use Yoyaku\Domain\WpUser\WpYoyakuOrganizerFactory;

class EventPeriodFactory extends AFactory
{
    /**
     * @param array $fields
     * @return EventPeriod
     * @throws InvalidArgumentException|Exception
     */
    public static function create($fields)
    {
        $period = new EventPeriod(
            new Id($fields['event_id']),
            new DateTimeValue(DateTimeService::get_custom_datetime_object_from_utc($fields['start_datetime'])),
            new DateTimeValue(DateTimeService::get_custom_datetime_object_from_utc($fields['end_datetime'])),
            new Count($fields['max_ticket_count']),
        );

        if (!empty($fields['id'])) {
            $period->set_id(new Id($fields['id']));
        }

        if (isset($fields['uuid'])) {
            $period->set_uuid(new Uuid4($fields['uuid']));
        }

        if (isset($fields['wp_id'])) {
            $period->set_wp_id(new Id($fields['wp_id']));
        }

        if (!empty($fields['location'])) {
            $period->set_location(new Address($fields['location']));
        }

        if (isset($fields['online_meeting_url'])) {
            $period->set_online_meeting_url(new Url($fields['online_meeting_url']));
        }

        if (isset($fields['zoom_meeting_id'])) {
            $period->set_zoom_meeting_id(new Id($fields['zoom_meeting_id']));
        }

        if (isset($fields['zoom_join_url'])) {
            $period->set_zoom_join_url(new Url($fields['zoom_join_url']));
        }

        if (isset($fields['zoom_start_url'])) {
            $period->set_zoom_start_url(new Url($fields['zoom_start_url']));
        }

        if (!empty($fields['google_calendar_event_id'])) {
            $period->set_google_calendar_event_id(new Label($fields['google_calendar_event_id']));
        }

        if (!empty($fields['google_meet_url'])) {
            $period->set_google_meet_url(new Url($fields['google_meet_url']));
        }

        if (isset($fields['wp_user_id'], $fields['wp_user_user_email'], $fields['wp_user_display_name'])) {
            $wp_user = WpYoyakuOrganizerFactory::create(
                [
                    'id' => $fields['wp_user_id'],
                    'user_email' => $fields['wp_user_user_email'],
                    'display_name' => $fields['wp_user_display_name'],
                ]
            );
            $period->set_wp_worker($wp_user);
        }
        return $period;
    }
}

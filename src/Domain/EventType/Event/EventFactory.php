<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\Event;

use Exception;
use InvalidArgumentException;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\Common\AFactory;
use Yoyaku\Domain\EventType\EventPeriod\EventPeriodFactory;
use Yoyaku\Domain\EventType\EventTicket\EventTicketFactory;
use Yoyaku\Domain\ValueObject\DateTime\Minutes;
use Yoyaku\Domain\ValueObject\Number\Count;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\String\Description;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Domain\ValueObject\String\Url;

class EventFactory extends AFactory
{
    /**
     * @param array $rows
     * @return Collection
     * @throws InvalidArgumentException|Exception
     */
    public static function create_collection($rows)
    {
        $events = [];
        $periods_grouped_by_event_id = [];
        $tickets_grouped_by_event_id = [];
        foreach ($rows as $fields) {
            // 未登録のイベントを追加
            if (!array_key_exists($fields['id'], $events)) {
                $events[$fields['id']] = self::create($fields);
            }

            // イベントにイベント期間を追加
            if (isset($fields['ep_id']) && !isset($periods_grouped_by_event_id[$fields['id']][$fields['ep_id']])) {
                $periods_grouped_by_event_id[$fields['id']][$fields['ep_id']] = [
                    'id' => $fields['ep_id'],
                    'uuid' => $fields['ep_uuid'],
                    'event_id' => $fields['ep_event_id'],
                    'wp_id' => $fields['ep_wp_id'],
                    'start_datetime' => $fields['ep_start_datetime'],
                    'end_datetime' => $fields['ep_end_datetime'],
                    'location' => $fields['ep_location'],
                    'max_ticket_count' => $fields['ep_max_ticket_count'],
                    'online_meeting_url' => $fields['ep_online_meeting_url'],
                    'zoom_meeting_id' => $fields['ep_zoom_meeting_id'],
                    'zoom_join_url' => $fields['ep_zoom_join_url'],
                    'zoom_start_url' => $fields['ep_zoom_start_url'],
                    'google_calendar_event_id' => $fields['ep_google_calendar_event_id'],
                    'google_meet_url' => $fields['ep_google_meet_url'],

                    'wp_user_id' => $fields['wp_user_id'],
                    'wp_user_user_email' => $fields['wp_user_user_email'],
                    'wp_user_display_name' => $fields['wp_user_display_name'],
                ];
            }
            // イベントにチケットを追加
            if (isset($fields['event_ticket_id']) && !isset($tickets_grouped_by_event_id[$fields['id']][$fields['event_ticket_id']])) {
                $tickets_grouped_by_event_id[$fields['id']][$fields['event_ticket_id']] = [
                    'id' => $fields['event_ticket_id'],
                    'event_id' => $fields['event_ticket_event_id'],
                    'name' => $fields['event_ticket_name'],
                    'ticket_count' => $fields['event_ticket_ticket_count'],
                    'price' => $fields['event_ticket_price'],
                ];
            }
        }

        foreach ($periods_grouped_by_event_id as $id => $event_periods) {
            $period_objs = EventPeriodFactory::create_collection($event_periods);
            $events[$id]->set_periods($period_objs);
        }

        foreach ($tickets_grouped_by_event_id as $id => $event_tickets) {
            $ticket_objs = EventTicketFactory::create_collection($event_tickets);
            $events[$id]->set_tickets($ticket_objs);
        }

        return new Collection($events);
    }

    /**
     * @param $fields
     * @return Event
     * @throws Exception
     */
    public static function create($fields)
    {
        $event = new Event(
            new Name($fields['name']),
            boolval($fields['use_approval_system']),
            new Minutes($fields['min_time_to_close_booking']),
            new Minutes($fields['min_time_to_cancel_booking']),
            new Count($fields['max_tickets_per_booking']),
            boolval($fields['is_online_payment'])
        );

        if (isset($fields['id'])) {
            $event->set_id(new Id($fields['id']));
        }

        if (isset($fields['description'])) {
            $event->set_description(new Description($fields['description']));
        }

        if (isset($fields['redirect_url'])) {
            $event->set_redirect_url(new Url($fields['redirect_url']));
        }

        if (isset($fields['show_worker'])) {
            $event->set_show_worker(boolval($fields['show_worker']));
        }

        if (isset($fields['event_ticket_id'])) {
            $event_ticket = EventTicketFactory::create([
                'id' => $fields['event_ticket_id'],
                'event_id' => $fields['event_ticket_event_id'],
                'name' => $fields['event_ticket_name'],
                'ticket_count' => $fields['event_ticket_ticket_count'],
                'price' => $fields['event_ticket_price'],
            ]);
            $event->set_tickets(new Collection([$event_ticket]));
        }

        return $event;
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType;

use Yoyaku\Application\Helper\HelperApplicationService;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\EventType\Event\Event;
use Yoyaku\Domain\EventType\EventBooking\EventBooking;
use Yoyaku\Domain\EventType\EventPeriod\EventPeriod;
use Yoyaku\Domain\EventType\EventTicket\BuyEventTicket;
use Yoyaku\Domain\Setting\SettingsService;

/**
 * Class EventBookingPlaceholdersData entity
 * イベント予約の通知で使うプレースホルダー用データ
 */
class EventBookingPlaceholdersData
{
    private Event $event;
    private EventPeriod $event_period;
    private EventBooking $booking;
    private Collection $buy_tickets;

    /**
     * @param Event $event
     * @param EventPeriod $event_period
     * @param EventBooking $booking
     * @param Collection<BuyEventTicket> $bought_tickets
     */
    public function __construct(
        Event        $event,
        EventPeriod  $event_period,
        EventBooking $booking,
        Collection   $bought_tickets,
    )
    {
        $this->event = $event;
        $this->event_period = $event_period;
        $this->booking = $booking;
        $this->buy_tickets = $bought_tickets;
    }

    /**
     * @return Event
     */
    public function get_event()
    {
        return $this->event;
    }

    /**
     * @return EventPeriod
     */
    public function get_event_period()
    {
        return $this->event_period;
    }

    /**
     * @return EventBooking
     */
    public function get_booking()
    {
        return $this->booking;
    }

    /**
     * @param $booking EventBooking
     */
    public function set_booking($booking)
    {
        $this->booking = $booking;
    }

    /**
     * @return string
     */
    public function get_booking_status()
    {
        return $this->booking->get_status()->value;
    }

    /**
     * @return array
     */
    public function get_placeholders_data()
    {
        return array_merge(
            $this->get_event_data(),
            $this->get_booking_data(),
        );
    }

    /**
     * @return array
     */
    private function get_event_data()
    {
        $start_datetime = $this->event_period->get_start_datetime()->get_value();
        $end_datetime = $this->event_period->get_end_datetime()->get_value();
        $offset = $start_datetime->getOffset();
        $start_timestamp_with_offset = $start_datetime->getTimestamp() + $offset;
        $end_timestamp_with_offset = $end_datetime->getTimestamp() + $offset;

        $event_tickets = [];
        foreach ($this->buy_tickets->get_items() as $ticket) {
            $event_tickets[] = $ticket->get_placeholder_text();
        }
        $event_tickets = implode('\n', $event_tickets);

        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        $date_time_format = $date_format . ' ' . $time_format;
        return [
            'event_name' => $this->event->get_name()->get_value(),
            'event_description' => $this->event->get_description()->get_value(),
            'event_location' => $this->event_period->get_location()->get_value(),
            'event_start_datetime' => date_i18n($date_time_format, $start_timestamp_with_offset),
            'event_end_datetime' => date_i18n($date_time_format, $end_timestamp_with_offset),
            'time_zone' => get_option('timezone_string'),
            'event_tickets' => $event_tickets,
            'online_meeting_url' => $this->event_period->get_online_meeting_url()->get_value(),
            'google_meet_url' => $this->event_period->get_google_meet_url()->get_value(),
            'zoom_join_url' => $this->event_period->get_zoom_join_url()->get_value(),
        ];
    }

    /**
     * @return array
     */
    private function get_booking_data()
    {
        $settings = SettingsService::get_instance();
        $cancel_url = $settings->get('cancel_url');
        $booking_cancel_url = '';
        if (!empty($cancel_url)) {
            $booking_cancel_url = $cancel_url . '?token=' . $this->booking->get_token()->get_value();
        }

        $booking_price = HelperApplicationService::get_formatted_price($this->booking->get_amount()->get_value());
        $first_name = $this->booking->get_first_name()->get_value();
        $last_name = $this->booking->get_last_name()->get_value();

        return array_merge(
            [
                'customer_email' => $this->booking->get_email()->get_value(),
                'customer_first_name' => $first_name,
                'customer_last_name' => $last_name,
                'customer_full_name' => $first_name . ' ' . $last_name,
                'customer_phone' => $this->booking->get_phone()->get_value(),
                'booking_price' => $booking_price,
                'booking_cancel_url' => $booking_cancel_url,
            ]
        );
    }
}

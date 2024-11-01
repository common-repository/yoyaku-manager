<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\EventTicketBooking;

use Yoyaku\Domain\ValueObject\Number\Count;
use Yoyaku\Domain\ValueObject\Number\Id;

class EventTicketBooking
{
    private ?Id $id = null;
    private Id $event_booking_id;
    private Id $event_ticket_id;
    /** @var Count 購入数 */
    private Count $buy_count;

    public function __construct(
        Id    $event_booking_id,
        Id    $event_ticket_id,
        Count $buy_count,
    )
    {
        $this->event_booking_id = $event_booking_id;
        $this->event_ticket_id = $event_ticket_id;
        $this->buy_count = $buy_count;
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
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     * @return Id
     */
    public function get_event_booking_id()
    {
        return $this->event_booking_id;
    }

    /**
     * @return Id
     */
    public function get_event_ticket_id()
    {
        return $this->event_ticket_id;
    }

    /**
     * @return Count
     */
    public function get_buy_count()
    {
        return $this->buy_count;
    }

    /**
     * @param Count $buy_count
     */
    public function set_buy_count($buy_count)
    {
        $this->buy_count = $buy_count;
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

    /**
     * @return array
     */
    public function to_array()
    {
        return [
            'id' => $this->get_id()?->get_value(),
            'event_booking_id' => $this->get_event_booking_id()->get_value(),
            'event_ticket_id' => $this->get_event_ticket_id()->get_value(),
            'buy_count' => $this->get_buy_count()->get_value(),
        ];
    }
}

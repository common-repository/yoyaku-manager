<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\EventTicket;

use Yoyaku\Domain\ValueObject\Number\Count;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\Number\Price;
use Yoyaku\Domain\ValueObject\String\Name;

/**
 * EventTicket Entity
 */
class EventTicket
{
    private ?Id $id = null;
    private Id $event_id;
    private Name $name;
    /** @var Count チケットの総数 */
    private Count $ticket_count;
    private Count $sold_ticket_count;
    private Price $price;

    public function __construct($event_id, $name, $count, $price)
    {
        $this->event_id = $event_id;
        $this->name = $name;
        $this->ticket_count = $count;
        $this->price = $price;
        $this->sold_ticket_count = new Count(0);
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
    public function get_event_id()
    {
        return $this->event_id;
    }

    /**
     * @param Id $event_id
     */
    public function set_event_id($event_id)
    {
        $this->event_id = $event_id;
    }

    /**
     * @return Name
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @param Name $name
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     * @return Count
     */
    public function get_ticket_count()
    {
        return $this->ticket_count;
    }

    /**
     * @param Count $ticket_count
     */
    public function set_ticket_count($ticket_count)
    {
        $this->ticket_count = $ticket_count;
    }

    /**
     * @return Count
     */
    public function get_sold_ticket_count()
    {
        return $this->sold_ticket_count;
    }

    /**
     * @param Count $sold_ticket_count
     */
    public function set_sold_ticket_count($sold_ticket_count)
    {
        $this->sold_ticket_count = $sold_ticket_count;
    }

    /**
     * @return Price
     */
    public function get_price()
    {
        return $this->price;
    }

    /**
     * @param Price $price
     */
    public function set_price($price)
    {
        $this->price = $price;
    }

    /**
     * @return array
     */
    public function to_table_data()
    {
        $result = $this->to_array();
        unset($result['id'], $result['sold_ticket_count']);
        return $result;
    }

    /**
     * @return array
     */
    public function to_array()
    {
        return [
            'id' => $this->get_id()?->get_value(),
            'event_id' => $this->get_event_id()->get_value(),
            'name' => $this->get_name()->get_value(),
            'price' => $this->get_price()->get_value(),
            'ticket_count' => $this->get_ticket_count()->get_value(),
            'sold_ticket_count' => $this->get_sold_ticket_count()->get_value(),
        ];
    }

    /**
     * @return array
     */
    public function to_array_for_customer()
    {
        return [
            'id' => $this->get_id()?->get_value(),
            'name' => $this->get_name()->get_value(),
            'price' => $this->get_price()->get_value(),
            'ticket_count' => $this->get_ticket_count()->get_value(),
            'sold_ticket_count' => $this->get_sold_ticket_count()->get_value(),
        ];
    }
}

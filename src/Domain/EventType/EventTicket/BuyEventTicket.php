<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\EventTicket;

use Yoyaku\Application\Helper\HelperApplicationService;
use Yoyaku\Domain\ValueObject\Number\Count;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\Number\Price;
use Yoyaku\Domain\ValueObject\String\Name;

/**
 * BuyEventTicket 値オブジェクト
 */
class BuyEventTicket
{
    /** @var Id ticket_id */
    private Id $id;
    private Name $name;
    /** @var Count 購入数 */
    private Count $buy_count;
    /** @var Count|null チケット数 */
    private ?Count $ticket_count = null;
    private ?Price $price = null;

    public function __construct($id, $buy_count)
    {
        $this->id = $id;
        $this->buy_count = $buy_count;
        $this->name = new Name();
    }

    /**
     * @return Id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @return Count
     */
    public function get_buy_count()
    {
        return $this->buy_count;
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
     * @return Count|null
     */
    public function get_ticket_count()
    {
        return $this->ticket_count;
    }

    /**
     * @param Count|null $ticket_count
     */
    public function set_ticket_count($ticket_count)
    {
        $this->ticket_count = $ticket_count;
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
     * @return string
     */
    public function get_placeholder_text()
    {
        if (!$this->price) {
            return "";
        }
        $name = $this->get_name()->get_value();
        $price = HelperApplicationService::get_formatted_price($this->get_price()->get_value());
        $count = $this->get_buy_count()->get_value();
        return "{$name} ({$price})  × {$count}";
    }

    /**
     * @return array
     */
    public function to_array()
    {
        return [
            'id' => $this->get_id()->get_value(),
            'count' => $this->get_buy_count()->get_value(),
            'name' => $this->get_name()?->get_value(),
            'price' => $this->get_price()?->get_value(),
            'ticket_count' => $this->get_ticket_count()?->get_value(),
        ];
    }
}

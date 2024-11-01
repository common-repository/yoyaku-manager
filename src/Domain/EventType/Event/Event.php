<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\Event;

use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\EventType\EventPeriod\EventPeriod;
use Yoyaku\Domain\EventType\EventTicket\EventTicket;
use Yoyaku\Domain\ValueObject\DateTime\Minutes;
use Yoyaku\Domain\ValueObject\Number\Count;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\String\Description;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Domain\ValueObject\String\Url;

class Event
{
    private ?Id $id = null;
    private Name $name;
    private Description $description;
    /**
     * @var bool 承認制ならtrue
     */
    private bool $use_approval_system;
    private bool $show_worker = false;
    private Url $redirect_url;
    /**
     * @var Minutes 予約受付締切 予約日時のx分前
     */
    private Minutes $min_time_to_close_booking;
    /**
     * @var Minutes キャンセル受付締め切り 予約日時のx分前
     */
    private Minutes $min_time_to_cancel_booking;
    private Count $max_tickets_per_booking;
    private bool $is_online_payment;

    private Collection $periods;
    private Collection $tickets;

    /**
     * @param Name $name
     * @param bool $use_approval_system
     * @param Minutes $min_time_to_close_booking
     * @param Minutes $min_time_to_cancel_booking
     * @param Count $max_tickets_per_booking
     * @param bool $is_online_payment
     */
    public function __construct(
        Name    $name,
        bool    $use_approval_system,
        Minutes $min_time_to_close_booking,
        Minutes $min_time_to_cancel_booking,
        Count   $max_tickets_per_booking,
        bool    $is_online_payment,
    )
    {
        $this->name = $name;
        $this->use_approval_system = $use_approval_system;
        $this->is_online_payment = $is_online_payment;
        $this->min_time_to_close_booking = $min_time_to_close_booking;
        $this->min_time_to_cancel_booking = $min_time_to_cancel_booking;
        $this->max_tickets_per_booking = $max_tickets_per_booking;
        $this->redirect_url = new Url();
        $this->description = new Description();

        $this->periods = new Collection();
        $this->tickets = new Collection();
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
     * @return Description
     */
    public function get_description()
    {
        return $this->description;
    }

    /**
     * @param Description $description
     */
    public function set_description($description)
    {
        $this->description = $description;
    }

    /**
     * @return Minutes
     */
    public function get_min_time_to_close_booking()
    {
        return $this->min_time_to_close_booking;
    }

    /**
     * @param Minutes $min_time_to_close_booking
     */
    public function set_min_time_to_close_booking($min_time_to_close_booking)
    {
        $this->min_time_to_close_booking = $min_time_to_close_booking;
    }

    /**
     * @return Minutes
     */
    public function get_min_time_to_cancel_booking()
    {
        return $this->min_time_to_cancel_booking;
    }

    /**
     * @param Minutes $min_time_to_cancel_booking
     */
    public function set_min_time_to_cancel_booking($min_time_to_cancel_booking)
    {
        $this->min_time_to_cancel_booking = $min_time_to_cancel_booking;
    }

    /**
     * @return Count
     */
    public function get_max_tickets_per_booking()
    {
        return $this->max_tickets_per_booking;
    }

    /**
     * @param Count $max_tickets_per_booking
     */
    public function set_max_tickets_per_booking($max_tickets_per_booking)
    {
        $this->max_tickets_per_booking = $max_tickets_per_booking;
    }

    /**
     * @return bool
     */
    public function get_use_approval_system()
    {
        return $this->use_approval_system;
    }

    /**
     * @param bool $use_approval_system
     */
    public function set_use_approval_system($use_approval_system)
    {
        $this->use_approval_system = $use_approval_system;
    }

    /**
     * @return bool
     */
    public function get_show_worker()
    {
        return $this->show_worker;
    }

    /**
     * @param bool $show_worker
     */
    public function set_show_worker($show_worker)
    {
        $this->show_worker = $show_worker;
    }

    /**
     * @return URL
     */
    public function get_redirect_url()
    {
        return $this->redirect_url;
    }

    /**
     * @param URL $redirect_url
     */
    public function set_redirect_url($redirect_url)
    {
        $this->redirect_url = $redirect_url;
    }

    /**
     * @return bool
     */
    public function get_is_online_payment()
    {
        return $this->is_online_payment;
    }

    /**
     * @param bool $is_online_payment
     */
    public function set_is_online_payment($is_online_payment)
    {
        $this->is_online_payment = $is_online_payment;
    }

    /**
     * @return Collection<EventPeriod>
     */
    public function get_periods()
    {
        return $this->periods;
    }

    /**
     * @param Collection<EventPeriod> $periods
     */
    public function set_periods($periods)
    {
        $this->periods = $periods;
    }

    /**
     * @return Collection<EventTicket>
     */
    public function get_tickets()
    {
        return $this->tickets;
    }

    /**
     * @param Collection<EventTicket> $tickets
     */
    public function set_tickets($tickets)
    {
        $this->tickets = $tickets;
    }

    /**
     * @return array
     */
    public function to_table_data()
    {
        $result = $this->to_array();
        unset($result['id'], $result['type'], $result['periods'], $result['tickets']);
        return $result;
    }

    /**
     * @return array
     */
    public function to_array()
    {
        return [
            'id' => $this->get_id()?->get_value(),
            'name' => $this->get_name()->get_value(),
            'use_approval_system' => $this->get_use_approval_system(),
            'max_tickets_per_booking' => $this->get_max_tickets_per_booking()->get_value(),
            'min_time_to_close_booking' => $this->get_min_time_to_close_booking()->get_value(),
            'min_time_to_cancel_booking' => $this->get_min_time_to_cancel_booking()->get_value(),
            'is_online_payment' => $this->get_is_online_payment(),
            'description' => $this->get_description()->get_value(),
            'show_worker' => $this->get_show_worker(),
            'redirect_url' => $this->get_redirect_url()->get_value(),
            'periods' => $this->get_periods()->to_array(),
            'tickets' => $this->get_tickets()->to_array(),
        ];
    }

    /**
     * @return array
     */
    public function to_array_for_customer()
    {
        $result = [
            'name' => $this->get_name()->get_value(),
            'is_online_payment' => $this->get_is_online_payment(),
            'description' => $this->get_description()->get_value(),
            'show_worker' => $this->get_show_worker(),
            'max_tickets_per_booking' => $this->get_max_tickets_per_booking()->get_value(),
            'redirect_url' => $this->get_redirect_url()->get_value(),
            'periods' => $this->get_periods()->to_array_for_customer(),
            'tickets' => $this->get_tickets()->to_array_for_customer(),
        ];
        if (!$this->get_show_worker()) {
            foreach ($result['periods'] as $index => $period) {
                $result['periods'][$index]['wp_worker'] = '';
            }
        }

        return $result;
    }
}

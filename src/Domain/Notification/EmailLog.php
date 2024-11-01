<?php declare(strict_types=1);

namespace Yoyaku\Domain\Notification;

use Exception;
use Yoyaku\Domain\DateTime\DateTimeService;
use Yoyaku\Domain\ValueObject\DateTime\DateTimeValue;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\String\Email;
use Yoyaku\Domain\ValueObject\String\Name;

/**
 * Entity
 */
class EmailLog
{
    private ?Id $id = null;
    private Id $customer_id;
    private DateTimeValue $sent_datetime;
    private bool $sent;
    private Email $to;
    private Name $subject;
    private Content $content;


    /**
     * @param $customer_id
     * @param $to
     * @param $subject
     * @param $content
     * @throws Exception
     */
    public function __construct($customer_id, $to, $subject, $content)
    {
        $this->customer_id = $customer_id;
        $this->sent_datetime = new DateTimeValue(DateTimeService::get_now_datetime_object_in_utc());
        $this->sent = false;
        $this->to = $to;
        $this->subject = $subject;
        $this->content = $content;
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
    public function set_id(Id $id)
    {
        $this->id = $id;
    }

    /**
     * @return Id
     */
    public function get_customer_id()
    {
        return $this->customer_id;
    }

    /**
     * @return DateTimeValue
     */
    public function get_sent_datetime()
    {
        return $this->sent_datetime;
    }

    /**
     * @param DateTimeValue $sent_datetime
     */
    public function set_sent_datetime($sent_datetime)
    {
        $this->sent_datetime = $sent_datetime;
    }

    /**
     * @return bool
     */
    public function get_sent()
    {
        return $this->sent;
    }

    /**
     * @param bool $sent
     */
    public function set_sent($sent)
    {
        $this->sent = $sent;
    }

    /**
     * @return Email
     */
    public function get_to()
    {
        return $this->to;
    }

    /**
     * @param Email $to
     */
    public function set_to(Email $to)
    {
        $this->to = $to;
    }

    /**
     * @return Name
     */
    public function get_subject()
    {
        return $this->subject;
    }

    /**
     * @param Name $subject
     */
    public function set_subject(Name $subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return Content
     */
    public function get_content()
    {
        return $this->content;
    }

    /**
     * @param Content $content
     */
    public function set_content(Content $content)
    {
        $this->content = $content;
    }

    /**
     * @return array
     */
    public function to_table_data()
    {
        $result = $this->to_array();
        $result['sent_datetime'] = $this->get_sent_datetime()->get_value_in_utc();

        unset($result['id']);

        return $result;
    }

    public function to_array(): array
    {
        return [
            'id' => $this->get_id()?->get_value(),
            'customer_id' => $this->get_customer_id()->get_value(),
            'sent_datetime' => $this->get_sent_datetime()->get_format_value(),
            'sent' => $this->get_sent(),
            'to' => $this->get_to()->get_value(),
            'subject' => $this->get_subject()->get_value(),
            'content' => $this->get_content()->get_value(),
        ];
    }
}

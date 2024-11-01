<?php declare(strict_types=1);

namespace Yoyaku\Domain\Notification;

use Yoyaku\Domain\ValueObject\DateTime\Days;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\String\Name;

class Notification
{
    private ?Id $id = null;
    private Name $name;
    private Name $subject;
    private Content $content;
    private NotificationTiming $timing;

    /**
     * @var Days|null 日数 イベント開始日のx日前や、イベント終了日のx日後
     */
    private ?Days $days = null;
    /**
     * @var TimeOfDay|null 送信時間
     */
    private ?TimeOfDay $time = null;
    /**
     * @var bool イベント開始前の通知ならtrue. 終了後の通知ならfalse
     */
    private bool $is_before = true;

    /**
     * @param Name $name
     * @param Name $subject
     * @param Content $content
     * @param NotificationTiming $timing
     */
    public function __construct(
        Name               $name,
        Name               $subject,
        Content            $content,
        NotificationTiming $timing,
    )
    {
        $this->name = $name;
        $this->subject = $subject;
        $this->content = $content;
        $this->timing = $timing;
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
     * @return Name
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @param Name $name
     */
    public function set_name(Name $name)
    {
        $this->name = $name;
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
     * @return NotificationTiming
     */
    public function get_timing()
    {
        return $this->timing;
    }

    /**
     * @param NotificationTiming $timing
     */
    public function set_timing(NotificationTiming $timing)
    {
        $this->timing = $timing;
    }

    /**
     * @return Days
     */
    public function get_days()
    {
        return $this->days;
    }

    /**
     * @param Days $days
     */
    public function set_days($days)
    {
        $this->days = $days;
    }

    /**
     * @return TimeOfDay
     */
    public function get_time()
    {
        return $this->time;
    }

    /**
     * @param TimeOfDay $time
     */
    public function set_time($time)
    {
        $this->time = $time;
    }

    /**
     * @return bool
     */
    public function is_before()
    {
        return $this->is_before;
    }

    /**
     * @param bool $is_before
     */
    public function set_is_before($is_before)
    {
        $this->is_before = $is_before;
    }

    /**
     * 定期通知ならture, 即時通知ならfalse
     * @return bool
     */
    public function is_scheduled()
    {
        return $this->timing === NotificationTiming::SCHEDULED;
    }

    /**
     * @return array
     */
    public function to_table_data()
    {
        $result = $this->to_array();

        if ($result['timing'] !== NotificationTiming::SCHEDULED->value) {
            $result['days'] = null;
            $result['time'] = null;
        }

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
            'name' => $this->get_name()->get_value(),
            'subject' => $this->get_subject()->get_value(),
            'content' => $this->get_content()->get_value(),
            'timing' => $this->get_timing()->value,
            'days' => $this->get_days()?->get_value(),
            'time' => $this->get_time()?->get_value(),
            'is_before' => $this->is_before(),
        ];
    }
}

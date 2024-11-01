<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\EventPeriod;

use DateInterval;
use DateTimeImmutable;
use InvalidArgumentException;
use Yoyaku\Domain\ValueObject\DateTime\DateTimeValue;
use Yoyaku\Domain\ValueObject\DateTime\Minutes;
use Yoyaku\Domain\ValueObject\Number\Count;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\String\Address;
use Yoyaku\Domain\ValueObject\String\Label;
use Yoyaku\Domain\ValueObject\String\Url;
use Yoyaku\Domain\ValueObject\String\Uuid4;
use Yoyaku\Domain\Worker\Worker;
use Yoyaku\Domain\WpUser\WpYoyakuWorker;


/**
 * Class EventPeriod
 */
class EventPeriod
{
    private ?Id $id = null;
    private Uuid4 $uuid;
    private Id $event_id;
    /**
     * @var Id|null 担当者
     */
    private ?Id $wp_id = null;
    /**
     * @var DateTimeValue 開始日時
     */
    private DateTimeValue $start_datetime;
    /**
     * @var DateTimeValue 終了日時 日付は $start_date_time と同じ
     */
    private DateTimeValue $end_datetime;
    private Address $location;
    /**
     * @var Count 予約上限数
     */
    private Count $max_ticket_count;
    private Url $online_meeting_url;
    private ?Id $zoom_meeting_id = null;
    private Url $zoom_join_url;
    private Url $zoom_start_url;
    private Url $google_meet_url;
    private Label $google_calendar_event_id;

    /**
     * @var Count|null チケットの販売枚数
     */
    private ?Count $sold_ticket_count = null;
    private ?WpYoyakuWorker $wp_worker = null;
    private ?Worker $worker = null;

    /**
     * @param Id $event_id
     * @param DateTimeValue $start_datetime
     * @param DateTimeValue $end_datetime
     * @param Count $max_ticket_count
     */
    public function __construct(
        Id            $event_id,
        DateTimeValue $start_datetime,
        DateTimeValue $end_datetime,
        Count         $max_ticket_count,
    )
    {
        $this->event_id = $event_id;

        // 開始日時　< 終了日時のチェック
        if ($end_datetime < $start_datetime) {
            throw new InvalidArgumentException("Invalid parameters: start_datetime is later than end_datetime.");
        }

        $this->start_datetime = $start_datetime;
        $this->end_datetime = $end_datetime;
        $this->max_ticket_count = $max_ticket_count;
        $this->uuid = new Uuid4();
        $this->location = new Address('');
        $this->online_meeting_url = new Url();
        $this->zoom_join_url = new Url();
        $this->zoom_start_url = new Url();
        $this->google_meet_url = new Url();
        $this->google_calendar_event_id = new Label();
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
     * @return Uuid4
     */
    public function get_uuid()
    {
        return $this->uuid;
    }

    /**
     * @param Uuid4 $uuid
     */
    public function set_uuid(Uuid4 $uuid)
    {
        $this->uuid = $uuid;
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
    public function set_event_id(Id $event_id)
    {
        $this->event_id = $event_id;
    }

    /**
     * @return Id
     */
    public function get_wp_id()
    {
        return $this->wp_id;
    }

    /**
     * @param Id $wp_id
     */
    public function set_wp_id($wp_id)
    {
        $this->wp_id = $wp_id;
    }

    /**
     * @return Address
     */
    public function get_location()
    {
        return $this->location;
    }

    /**
     * @param Address $location
     */
    public function set_location(Address $location)
    {
        $this->location = $location;
    }

    /**
     * @return DateTimeValue
     */
    public function get_start_datetime()
    {
        return $this->start_datetime;
    }

    /**
     * @return DateTimeValue
     */
    public function get_end_datetime()
    {
        return $this->end_datetime;
    }

    /**
     * @return Count
     */
    public function get_max_ticket_count()
    {
        return $this->max_ticket_count;
    }

    /**
     * @param Count $max_ticket_count
     */
    public function set_max_ticket_count($max_ticket_count)
    {
        $this->max_ticket_count = $max_ticket_count;
    }

    /**
     * @return Id|null
     */
    public function get_zoom_meeting_id()
    {
        return $this->zoom_meeting_id;
    }

    /**
     * @param Id|null $zoom_meeting_id
     */
    public function set_zoom_meeting_id(?Id $zoom_meeting_id)
    {
        $this->zoom_meeting_id = $zoom_meeting_id;
    }

    /**
     * @return Url
     */
    public function get_zoom_start_url()
    {
        return $this->zoom_start_url;
    }

    /**
     * @param Url $zoom_start_url
     */
    public function set_zoom_start_url(Url $zoom_start_url)
    {
        $this->zoom_start_url = $zoom_start_url;
    }

    /**
     * @return Url
     */
    public function get_online_meeting_url()
    {
        return $this->online_meeting_url;
    }

    /**
     * @param Url $online_meeting_url
     */
    public function set_online_meeting_url(Url $online_meeting_url)
    {
        $this->online_meeting_url = $online_meeting_url;
    }

    /**
     * @return Url
     */
    public function get_zoom_join_url()
    {
        return $this->zoom_join_url;
    }

    /**
     * @param Url $zoom_join_url
     */
    public function set_zoom_join_url(Url $zoom_join_url)
    {
        $this->zoom_join_url = $zoom_join_url;
    }

    /**
     * @return Label
     */
    public function get_google_calendar_event_id()
    {
        return $this->google_calendar_event_id;
    }

    /**
     * @param Label $google_calendar_event_id
     */
    public function set_google_calendar_event_id($google_calendar_event_id)
    {
        $this->google_calendar_event_id = $google_calendar_event_id;
    }

    /**
     * @return Url
     */
    public function get_google_meet_url()
    {
        return $this->google_meet_url;
    }

    /**
     * @param Url $google_meet_url
     */
    public function set_google_meet_url($google_meet_url)
    {
        $this->google_meet_url = $google_meet_url;
    }

    /**
     * @return Count
     */
    public function get_sold_ticket_count()
    {
        return $this->sold_ticket_count;
    }

    /**
     * @param Count|null $sold_ticket_count
     */
    public function set_sold_ticket_count($sold_ticket_count)
    {
        $this->sold_ticket_count = $sold_ticket_count;
    }

    /**
     * @return WpYoyakuWorker
     */
    public function get_wp_worker()
    {
        return $this->wp_worker;
    }

    /**
     * @param WpYoyakuWorker $wp_worker
     */
    public function set_wp_worker($wp_worker)
    {
        $this->wp_worker = $wp_worker;
    }

    /**
     * @return Worker
     */
    public function get_worker()
    {
        return $this->worker;
    }

    /**
     * @param Worker $worker
     */
    public function set_worker($worker)
    {
        $this->worker = $worker;
    }

    /**
     * チケットの残り枚数を取得
     * @return int|null sold_ticket_countがセットされているならチケットの残り枚数。されていないならnull
     */
    public function get_rest_ticket_count()
    {
        if (is_null($this->sold_ticket_count)) {
            return null;
        } else {
            return $this->max_ticket_count->get_value() - $this->sold_ticket_count->get_value();
        }
    }

    /**
     * 開始日時と終了日時の間隔（分）を取得 （zoom api用）
     * @return int
     */
    public function get_duration()
    {
        $start_dt = $this->get_start_datetime()->get_value();
        $end_dt = $this->get_end_datetime()->get_value();
        $diff = $start_dt->diff($end_dt);
        return $diff->d * 24 * 60 + $diff->h * 60 + $diff->i;
    }

    /**
     * 予約可能かチェックする
     * @param Minutes $min_time_to_close_booking 開始日時の$min_time_to_close_booking分前まで受付
     * @param DateTimeImmutable $now 現在日時
     * @return bool
     */
    public function can_book_now($min_time_to_close_booking, $now)
    {
        $start_dt = $this->get_start_datetime()->get_value();
        $deadline_dt = $start_dt->sub(new DateInterval("PT{$min_time_to_close_booking->get_value()}M"));
        return $now <= $deadline_dt;
    }

    /**
     * キャンセル可能かチェックする
     * @param Minutes $min_time_to_cancel_booking 開始日時の$min_time_to_cancel_booking分前まで受付
     * @param DateTimeImmutable $now 現在日時
     * @return bool
     */
    public function can_cancel_now($min_time_to_cancel_booking, $now)
    {
        $start_dt = $this->get_start_datetime()->get_value();
        $deadline_dt = $start_dt->sub(new DateInterval("PT{$min_time_to_cancel_booking->get_value()}M"));
        return $now <= $deadline_dt;
    }

    /**
     * @return array
     */
    public function to_table_data()
    {
        $result = $this->to_array();
        $result['start_datetime'] = $this->get_start_datetime()->get_value_in_utc();
        $result['end_datetime'] = $this->get_end_datetime()->get_value_in_utc();
        unset(
            $result['id'],
            $result['worker'],
            $result['wp_worker'],
            $result['rest_ticket_count'],
            $result['sold_ticket_count']
        );

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
            'wp_id' => $this->get_wp_id()?->get_value(),
            'uuid' => $this->get_uuid()->get_value(),
            'location' => $this->get_location()->get_value(),
            'start_datetime' => $this->get_start_datetime()->get_format_value(),
            'end_datetime' => $this->get_end_datetime()->get_format_value(),
            'max_ticket_count' => $this->get_max_ticket_count()->get_value(),
            'online_meeting_url' => $this->get_online_meeting_url()->get_value(),
            'zoom_meeting_id' => $this->get_zoom_meeting_id()?->get_value(),
            'zoom_start_url' => $this->get_zoom_start_url()->get_value(),
            'zoom_join_url' => $this->get_zoom_join_url()->get_value(),
            'google_calendar_event_id' => $this->get_google_calendar_event_id()->get_value(),
            'google_meet_url' => $this->get_google_meet_url()->get_value(),

            'sold_ticket_count' => $this->get_sold_ticket_count()?->get_value(),
            'rest_ticket_count' => 0 < $this->get_rest_ticket_count() ? $this->get_rest_ticket_count() : 0,
            'wp_worker' => $this->wp_worker?->get_display_name()->get_value() ?: '',
            'worker' => $this->worker?->to_array(),
        ];
    }

    /**
     * @return array
     */
    public function to_array_for_customer()
    {
        return [
            'uuid' => $this->get_uuid()->get_value(),
            'location' => $this->get_location()->get_value(),
            'start_datetime' => $this->get_start_datetime()->get_format_value(),
            'end_datetime' => $this->get_end_datetime()->get_format_value(),
            'rest_ticket_count' => 0 < $this->get_rest_ticket_count() ? $this->get_rest_ticket_count() : 0,
            'wp_worker' => $this->wp_worker?->get_display_name()->get_value() ?: '',
        ];
    }
}

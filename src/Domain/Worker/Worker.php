<?php declare(strict_types=1);

namespace Yoyaku\Domain\Worker;

use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\String\Name;

/**
 * ワーカークラス Entity
 */
class Worker
{
    private Id $id;
    private Name $zoom_user_id;
    private AccessToken $google_calendar_token;
    private GoogleCalendarId $google_calendar_id;

    /**
     * @param Id $id
     */
    public function __construct(Id $id)
    {
        $this->id = $id;
        $this->zoom_user_id = new Name();
        $this->google_calendar_token = new AccessToken();
        $this->google_calendar_id = new GoogleCalendarId();
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
    public function get_zoom_user_id()
    {
        return $this->zoom_user_id;
    }

    /**
     * @param Name $zoom_user_id
     */
    public function set_zoom_user_id(Name $zoom_user_id)
    {
        $this->zoom_user_id = $zoom_user_id;
    }

    /**
     * @return GoogleCalendarId
     */
    public function get_google_calendar_id()
    {
        return $this->google_calendar_id;
    }

    /**
     * @param GoogleCalendarId $google_calendar_id
     */
    public function set_google_calendar_id($google_calendar_id)
    {
        $this->google_calendar_id = $google_calendar_id;
    }

    /**
     * @return AccessToken
     */
    public function get_google_calendar_token()
    {
        return $this->google_calendar_token;
    }

    /**
     * @param AccessToken $google_calendar_token
     */
    public function set_google_calendar_token($google_calendar_token)
    {
        $this->google_calendar_token = $google_calendar_token;
    }

    /**
     * @return array
     */
    public function to_table_data()
    {
        return $this->to_array();
    }

    public function to_array()
    {
        return [
            'id' => $this->get_id()->get_value(),
            'zoom_user_id' => $this->get_zoom_user_id()->get_value(),
            'google_calendar_id' => $this->get_google_calendar_id()->get_value(),
            'google_calendar_token' => $this->get_google_calendar_token()->get_value(),
        ];
    }
}

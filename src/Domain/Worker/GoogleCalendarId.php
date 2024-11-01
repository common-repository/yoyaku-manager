<?php declare(strict_types=1);

namespace Yoyaku\Domain\Worker;

/**
 * GoogleCalendarId 空文字許可
 */
final class GoogleCalendarId
{
    private string $calendar_id;

    /**
     * @param string $calendar_id
     */
    public function __construct($calendar_id = '')
    {
        $this->calendar_id = trim($calendar_id);
    }

    public function get_value(): string
    {
        return $this->calendar_id;
    }
}

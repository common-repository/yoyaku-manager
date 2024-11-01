<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject\DateTime;

use DateTime;
use InvalidArgumentException;

/**
 * 日付クラス 値オブジェクト
 */
final class DateValue
{
    private DateTime $date;

    /**
     * @param string $date
     * @throws InvalidArgumentException
     */
    public function __construct(string $date)
    {
        $dt = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dt) {
            throw new InvalidArgumentException("date must be Y-m-d format.");
        }
        $this->date = $dt;
    }

    public function get_value(): string
    {
        return $this->date->format('Y-m-d');
    }
}

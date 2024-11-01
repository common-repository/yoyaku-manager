<?php declare(strict_types=1);

namespace Yoyaku\Domain\Customer;

use DateTime;
use InvalidArgumentException;

/**
 * 誕生日クラス 値オブジェクト
 */
final class Birthday
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
            throw new InvalidArgumentException("Birthday must be Y-m-d format.");
        }
        $this->date = $dt;
    }

    public function get_value(): string
    {
        return $this->date->format('Y-m-d');
    }
}

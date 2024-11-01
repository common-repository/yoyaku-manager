<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject\Number;

use InvalidArgumentException;

/**
 * 0以上の整数
 */
class Count
{
    private int $integer;

    /**
     * @param int|string $integer
     * @throws InvalidArgumentException
     */
    public function __construct($integer)
    {
        if (filter_var($integer, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) === false) {
            throw new InvalidArgumentException('Number must be integer');
        }

        $this->integer = (int)$integer;
    }

    public function get_value(): int
    {
        return $this->integer;
    }
}

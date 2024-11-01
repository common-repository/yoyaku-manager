<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject\Number;

use InvalidArgumentException;

/**
 * ポジション 値オブジェクト
 */
final class Position
{
    private int $integer;

    /**
     * @param int|string $integer
     * @throws InvalidArgumentException
     */
    public function __construct($integer)
    {
        if (filter_var($integer, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            throw new InvalidArgumentException("Position must be greater than 0");
        }

        $this->integer = (int)$integer;
    }

    public function get_value(): int
    {
        return $this->integer;
    }
}

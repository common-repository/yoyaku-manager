<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject\Number;

use InvalidArgumentException;

/**
 * Id 0以上の整数をidとする
 */
final class Id
{
    private int $id;

    /**
     * @param int|string $id
     */
    public function __construct($id)
    {
        if (filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) === false) {
            throw new InvalidArgumentException('Id must be 0 or more');
        }

        $this->id = (int)$id;
    }

    public function get_value(): int
    {
        return $this->id;
    }
}

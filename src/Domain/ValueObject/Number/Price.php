<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject\Number;

use InvalidArgumentException;

/**
 * 価格 米国は価格に小数点を使うためfloat型にしている
 */
final class Price
{
    private float|int $price;

    /**
     * @param float|int $price
     * @throws InvalidArgumentException
     */
    public function __construct($price)
    {
        if ($price < 0) {
            throw new InvalidArgumentException('Price must be larger then or equal to 0');
        }
        $this->price = $price;
    }

    public function get_value(): float|int
    {
        return $this->price;
    }
}

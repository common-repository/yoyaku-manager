<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject\String;

use InvalidArgumentException;

/**
 * 住所 空文字許可 値オブジェクト
 */
final class Address
{
    const MAX_LENGTH = 255;
    private string $address;

    /**
     * @param string $address
     * @throws InvalidArgumentException
     */
    public function __construct($address)
    {
        if (self::MAX_LENGTH < strlen($address)) {
            throw new InvalidArgumentException('Address must be less than 255 chars');
        }
        $this->address = $address;
    }

    public function get_value(): string
    {
        return $this->address;
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject\String;

use InvalidArgumentException;

/**
 * ISO 3166-1 で規定されている国名コード
 */
final class CountryCode
{
    const MAX_LENGTH = 2;
    private string $name;

    /**
     * @param string $name
     * @throws InvalidArgumentException
     */
    public function __construct($name = '')
    {
        if (strlen($name) != 0 && self::MAX_LENGTH != strlen($name)) {
            throw new InvalidArgumentException('Name must be less than 2 chars');
        }

        $this->name = strtoupper($name);
    }

    public function get_value(): string
    {
        return $this->name;
    }
}

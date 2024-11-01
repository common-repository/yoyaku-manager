<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject\String;

use InvalidArgumentException;

/**
 * 名前 空文字許可
 */
final class Name
{
    const MAX_LENGTH = 255;
    private string $name;

    /**
     * @param string $name
     * @throws InvalidArgumentException
     */
    public function __construct($name = '')
    {
        if (self::MAX_LENGTH < strlen($name)) {
            throw new InvalidArgumentException('Name must be less than 255 chars');
        }

        $this->name = trim($name);
    }

    public function get_value(): string
    {
        return $this->name;
    }
}

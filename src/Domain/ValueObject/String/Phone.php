<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject\String;

use InvalidArgumentException;

/**
 * 電話番号 空文字許可
 */
final class Phone
{
    const MAX_LENGTH = 30;
    private string $phone;

    /**
     * @param string $phone
     * @throws InvalidArgumentException
     */
    public function __construct($phone = '')
    {
        if ($phone && self::MAX_LENGTH < strlen($phone)) {
            throw new InvalidArgumentException('Phone must be less than 30 chars');
        }
        $this->phone = $phone;
    }

    public function get_value(): string
    {
        return $this->phone;
    }
}

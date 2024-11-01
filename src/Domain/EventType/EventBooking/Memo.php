<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\EventBooking;

use InvalidArgumentException;

/**
 * メモ 値オブジェクト
 */
final class Memo
{
    const MAX_LENGTH = 5000;
    private string $memo;

    /**
     * @param string $memo
     * @throws InvalidArgumentException
     */
    public function __construct($memo = '')
    {
        if (self::MAX_LENGTH < strlen($memo)) {
            throw new InvalidArgumentException('Memo must be less than 5000 chars');
        }
        $this->memo = $memo;
    }

    public function get_value(): string
    {
        return $this->memo;
    }
}

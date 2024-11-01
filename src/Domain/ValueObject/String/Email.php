<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject\String;

use InvalidArgumentException;

/**
 * Emailクラス 空文字許可
 */
final class Email
{
    const MAX_LENGTH = 255;
    private string $email;

    /**
     * @param string $email
     * @throws InvalidArgumentException
     */
    public function __construct($email)
    {
        if (self::MAX_LENGTH < strlen($email)) {
            throw new InvalidArgumentException('Email must be less than 255 chars');
        }
        if ($email !== '' && !is_email($email)) {
            throw new InvalidArgumentException('Email is not a valid email');
        }
        $this->email = $email;
    }

    public function get_value(): string
    {
        return $this->email;
    }
}

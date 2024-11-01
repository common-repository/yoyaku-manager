<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject\String;

use InvalidArgumentException;

/**
 * URL 空文字許可
 */
final class Url
{
    const MAX_LENGTH = 4096;
    private string $url;

    /**
     * @param string $url
     * @throws InvalidArgumentException
     */
    public function __construct($url = '')
    {
        if (self::MAX_LENGTH < strlen($url)) {
            throw new InvalidArgumentException('URL must be less than 4096 chars');
        }
        if ($url && filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException("Invalid URL format");
        }
        $this->url = $url;
    }

    public function get_value(): string
    {
        return $this->url;
    }
}

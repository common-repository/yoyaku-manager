<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject\String;

use InvalidArgumentException;

/**
 * 説明文, ディスクリプション
 */
final class Description
{
    const MAX_LENGTH = 5000;
    private string $description;

    /**
     * @param string $description
     * @throws InvalidArgumentException
     */
    public function __construct($description = '')
    {
        if (self::MAX_LENGTH < strlen($description)) {
            throw new InvalidArgumentException(
                'Description must be less than 5000 chars'
            );
        }
        $this->description = $description;
    }

    public function get_value(): string
    {
        return $this->description;
    }
}

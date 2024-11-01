<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject\String;

use InvalidArgumentException;

/**
 * カラーコードのクラス #000000 形式
 */
final class Color
{
    const MAX_LENGTH = 255;
    private string $color;

    /**
     * @param string $color
     * @throws InvalidArgumentException
     */
    public function __construct($color = '')
    {
        if ($color && !preg_match('/^#([\da-fA-F]{6}|[\da-fA-F]{3})$/', $color)) {
            throw new InvalidArgumentException("Color must be #000000 format");
        }

        $this->color = $color;
    }

    public function get_value(): string
    {
        return $this->color;
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject\String;

use InvalidArgumentException;

/**
 * ラベル 空文字許可
 */
final class Label
{
    const MAX_LENGTH = 65535;
    private string $label;

    /**
     * @param string $label
     * @throws InvalidArgumentException
     */
    public function __construct($label = '')
    {
        if (self::MAX_LENGTH < strlen($label)) {
            throw new InvalidArgumentException('Label must be less than 65535 chars');
        }
        $this->label = $label;
    }

    public function get_value(): string
    {
        return $this->label;
    }
}

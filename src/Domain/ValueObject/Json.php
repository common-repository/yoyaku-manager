<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject;

use InvalidArgumentException;

/**
 * Class Json
 */
final class Json
{
    private string $value;

    /**
     * @param string $value
     */
    public function __construct($value = '{}')
    {
        if (!json_decode($value)) {
            throw new InvalidArgumentException('value is not valid Json data');
        }
        $this->value = $value;
    }

    public function get_value(): string
    {
        return $this->value;
    }

    public function decode(): array
    {
        return json_decode($this->value, true);
    }
}

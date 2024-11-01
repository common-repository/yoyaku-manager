<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject\String;


use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

/**
 * uuid
 */
final class Uuid4
{
    private string $uuid;

    /**
     * @param string $uuid
     */
    public function __construct($uuid = '')
    {
        $this->uuid = $uuid ?: Uuid::uuid4()->toString();
        if (!Uuid::isValid($this->uuid)) {
            throw new InvalidArgumentException("Invalid uuid");
        }
    }

    public function get_value(): string
    {
        return $this->uuid;
    }
}

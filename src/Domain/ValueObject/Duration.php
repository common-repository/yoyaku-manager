<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject;

use InvalidArgumentException;

/**
 * Durationクラス
 * サービスの'時間間隔の前'や'時間間隔の後'などで使う。
 */
final class Duration
{
    private int $duration;

    /**
     * @param int $duration
     * @throws InvalidArgumentException
     */
    public function __construct($duration)
    {
        $this->duration = $duration;
    }

    public function get_value(): int
    {
        return $this->duration;
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Domain\Notification;

use InvalidArgumentException;

/**
 * 時刻(時分秒)クラス 値オブジェクト
 * hh:mm:ss 形式 24時間制 0埋め
 */
final class TimeOfDay
{
    private string $value;

    /**
     * @param string $value
     * @throws InvalidArgumentException
     */
    public function __construct($value)
    {
        if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $value)) {
            throw new InvalidArgumentException('TimeOfDay is invalid format.');
        }
        $this->value = $value;
    }

    public function get_value(): string
    {
        return $this->value;
    }
}

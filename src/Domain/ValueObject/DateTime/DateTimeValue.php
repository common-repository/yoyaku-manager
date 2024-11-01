<?php declare(strict_types=1);

namespace Yoyaku\Domain\ValueObject\DateTime;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

/**
 * DateTimeValue 値オブジェクト
 */
final class DateTimeValue
{
    private DateTimeImmutable $date;

    /**
     * @param DateTimeImmutable $date
     * @throws InvalidArgumentException
     */
    public function __construct(DateTimeImmutable $date)
    {
        $this->date = $date;
    }

    public function get_value(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * 文字列に変換した日時を取得
     * @param string $format 日時のフォーマット
     * @return string
     */
    public function get_format_value($format = DATE_RFC3339): string
    {
        return $this->date->format($format);
    }

    /**
     * utc時間の日時を取得. 主にDBに保存するときに使う
     * @return string
     */
    public function get_value_in_utc(): string
    {
        return $this->date->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    }

    /**
     * 日付を取得
     * @return string
     */
    public function get_date(): string
    {
        return $this->date->format('Y-m-d');
    }

    /**
     * 日付を取得
     * @return string
     */
    public function get_time(): string
    {
        return $this->date->format('H:i:s');
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Domain\Notification;

use InvalidArgumentException;

/**
 * Class Content 値オブジェクト
 */
final class Content
{
    // MySQLのTEXT型の最大長が65535
    const MAX_LENGTH = 65535;
    private string $html;

    /**
     * @param string $html
     * @throws InvalidArgumentException
     */
    public function __construct($html)
    {
        if (self::MAX_LENGTH < strlen($html)) {
            throw new InvalidArgumentException(
                'Content must be less than 65535 chars'
            );
        }
        $this->html = $html;
    }

    public function get_value(): string
    {
        return $this->html;
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Application\Common\Exceptions;

use Exception;

/**
 * DB接続エラーなど予期せぬ例外
 */
class WpDbException extends Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message = 'query_execution_error', $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

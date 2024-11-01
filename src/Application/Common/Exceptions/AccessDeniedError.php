<?php declare(strict_types=1);

namespace Yoyaku\Application\Common\Exceptions;

use Exception;

/**
 * 権限不足エラー
 */
class AccessDeniedError extends Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message = 'not allowed action', $code = 403, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

<?php declare(strict_types=1);

namespace Yoyaku\Application\Common\Exceptions;

use Exception;

/**
 * データ重複エラー
 */
class DuplicationError extends Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message = 'duplication_error', $code = 400, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

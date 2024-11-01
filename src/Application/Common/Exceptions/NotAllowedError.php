<?php declare(strict_types=1);

namespace Yoyaku\Application\Common\Exceptions;

use Exception;

/**
 * 仕様により禁止されている操作を実行しようとした時に発生する例外
 */
class NotAllowedError extends Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message = 'not_allowed_operation', $code = 400, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

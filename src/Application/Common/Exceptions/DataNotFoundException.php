<?php declare(strict_types=1);

namespace Yoyaku\Application\Common\Exceptions;

use Exception;

class DataNotFoundException extends Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message = '', $code = 404, Exception $previous = null)
    {
        parent::__construct(
            $message ?: __('Data not found.', 'yoyaku-manager'),
            $code,
            $previous,
        );
    }
}

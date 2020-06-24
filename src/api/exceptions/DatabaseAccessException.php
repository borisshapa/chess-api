<?php


namespace api\exceptions;

use Throwable;

/**
 * Class DatabaseAccessException
 * Error accessing database.
 * @package api\exceptions
 */
class DatabaseAccessException extends ChessApiException
{
    /**
     * DatabaseAccessException constructor.
     * @param string $message error message
     * @param int $code error code
     * @param Throwable|null $previous {@link Throwable} object
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("DatabaseAccessException : " . $message, $code, $previous);
    }
}
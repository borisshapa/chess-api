<?php


namespace api\exceptions;

use Throwable;

/**
 * Class ChessApiException
 * Error during interaction with api.
 * @package api\exceptions
 */
class ChessApiException extends \Exception
{
    /**
     * ChessApiException constructor.
     * @param string $message error message
     * @param int $code error code
     * @param Throwable|null $previous {@link Throwable} object
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("ChessApiException : " . $message, $code, $previous);
    }
}
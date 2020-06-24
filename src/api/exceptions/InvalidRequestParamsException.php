<?php


namespace api\exceptions;

use Throwable;

/**
 * Class InvalidRequestParamsException
 * The error occurs when there is no necessary parameter when accessing the api methods.
 * @package api\exceptions
 */
class InvalidRequestParamsException extends ChessApiException
{
    /**
     * InvalidRequestParamsException constructor.
     * @param string $message error message
     * @param int $code error code
     * @param Throwable|null $previous {@link Throwable} object
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("InvalidRequestParamsException : " . $message, $code, $previous);
    }
}
<?php


namespace api\exceptions;

use Throwable;

/**
 * Class InvalidRequestParamsException
 * The error occurs when there is no necessary parameter when accessing the api methods.
 * @package api\exceptions
 * @see ChessApiException
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class InvalidRequestParamsException extends ChessApiException
{
    /**
     * InvalidRequestParamsException constructor.
     * @param string $message error message
     * @param int $code error code
     * @param Throwable|null $previous {@see Throwable} object
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("InvalidRequestParamsException : " . $message, $code, $previous);
    }
}
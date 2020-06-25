<?php

namespace api\exceptions;

use Exception;
use Throwable;

/**
 * Class ChessApiException
 * Error during interaction with chess API.
 * @package api\exceptions
 * @see Exception
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class ChessApiException extends Exception
{
    /**
     * ChessApiException constructor.
     * @param string $message error message
     * @param int $code error code
     * @param Throwable|null $previous {@see Throwable} object
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("ChessApiException : " . $message, $code, $previous);
    }
}
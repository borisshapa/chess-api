<?php


namespace app\chess\exceptions;

use Exception;
use Throwable;

/**
 * Class ChessException
 * Error related to violation of chess rules.
 * @package app\chess\exceptions
 * @see Exception
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class ChessException extends Exception
{
    /**
     * ChessException constructor.
     * @param string $message error message
     * @param int $code error code
     * @param Throwable|null $previous {@see Throwable} object
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("ChessException : " . $message, $code, $previous);
    }
}
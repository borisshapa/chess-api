<?php


namespace app\chess\exceptions;

use Throwable;

/**
 * Class InvalidMoveException
 * Error related to the wrong move.
 * @package app\chess\exceptions
 * @see \Exception
 * @see ChessException
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class InvalidMoveException extends ChessException
{
    /**
     * InvalidMoveException constructor.
     * @param string $message error message
     * @param int $code error code
     * @param Throwable|null $previous {@see Throwable} object
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("InvalidMoveException : " . $message, $code, $previous);
    }
}
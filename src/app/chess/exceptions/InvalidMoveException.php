<?php


namespace app\chess\exceptions;

use Throwable;

class InvalidMoveException extends ChessException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("InvalidMoveException : " . $message, $code, $previous);
    }
}
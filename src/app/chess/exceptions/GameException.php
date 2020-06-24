<?php


namespace app\chess\exceptions;


use Throwable;

class GameException extends ChessException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("GameException : " . $message, $code, $previous);
    }
}
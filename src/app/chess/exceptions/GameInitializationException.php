<?php


namespace app\chess\exceptions;


use Throwable;

class GameInitializationException extends GameException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("GameInitializationException : " . $message, $code, $previous);
    }
}
<?php


namespace app\chess\exceptions;


use Throwable;

class ChessException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("ChessException : " . $message, $code, $previous);
    }
}
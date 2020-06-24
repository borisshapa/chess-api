<?php


namespace api\exceptions;


use Throwable;

class ChessApiException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("ChessApiException : " . $message, $code, $previous);
    }
}
<?php


namespace api\exceptions;


use Throwable;

class InvalidRequestParamsException extends ChessApiException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("InvalidRequestParamsException : " . $message, $code, $previous);
    }
}
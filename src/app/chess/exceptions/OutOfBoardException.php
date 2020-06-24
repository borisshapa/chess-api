<?php


namespace app\chess\exceptions;


use app\chess\board\Position;
use Throwable;

class OutOfBoardException extends ChessException
{
    public function __construct(int $rows, int $cols, Position $requestedPosition,
                                $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("OutOfBoardException : "
            . "Board size: {$rows} x {$cols}, requested position: ({$requestedPosition->getRow()}, {$requestedPosition->getCol()})",
            $code, $previous);
    }
}
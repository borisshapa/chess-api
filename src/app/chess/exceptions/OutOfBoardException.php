<?php


namespace app\chess\exceptions;


use app\chess\board\Position;
use Throwable;

/**
 * Class OutOfBoardException
 * Error accessing a nonexistent square on the board.
 * @package app\chess\exceptions
 * @see \Exception
 * @see ChessException
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class OutOfBoardException extends ChessException
{
    /**
     * OutOfBoardException constructor.
     * @param int $rows number of rows of the board
     * @param int $cols number of columns of the board
     * @param Position $requestedPosition non-existent position that was an attempt to appeal
     * @param int $code error code
     * @param Throwable|null $previous {@see Throwable} object
     */
    public function __construct(int $rows, int $cols, Position $requestedPosition, $code = 0, Throwable $previous = null)
    {
        parent::__construct("OutOfBoardException : "
            . "Board size: {$rows} x {$cols}, requested position: ({$requestedPosition->getRow()}, {$requestedPosition->getCol()})",
            $code, $previous);
    }
}
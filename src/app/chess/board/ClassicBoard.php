<?php

namespace app\chess\board;

/**
 * Class ClassicBoard
 * Classic chess board 8x8.
 * @package app\chess\board
 * @see Board
 * @see AbstractRectangularBoard
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class ClassicBoard extends AbstractRectangularBoard
{
    /**
     * Number of rows of a classic chess board.
     */
    public const CLASSIC_BOARD_ROWS = 8;

    /**
     * Number of columns of a classic chess board.
     */
    public const CLASSIC_BOARD_COLS = 8;

    /**
     * ClassicBoard constructor.
     */
    public function __construct()
    {
        parent::__construct(self::CLASSIC_BOARD_ROWS, self::CLASSIC_BOARD_COLS);
    }
}
<?php

namespace app\chess\board;

class ClassicBoard extends AbstractRectangularBoard
{
    public const CLASSIC_BOARD_ROWS = 8;
    public const CLASSIC_BOARD_COLS = 8;

    public function __construct()
    {
        parent::__construct(self::CLASSIC_BOARD_ROWS, self::CLASSIC_BOARD_COLS);
    }
}
<?php

namespace chess\board;

use chess\util\Constants;

include "Board.php";
include "../Constants.php";
include "Position.php";
include "../exceptions/ChessExceptions.php";


abstract class AbstractBoard implements Board
{
    private int $rows;
    private int $cols;

    protected function __construct(int $rows = Constants::CLASSIC_BOARD_ROWS,
                                int $cols = Constants::CLASSIC_BOARD_COLS)
    {
        $this->rows = $rows;
        $this->cols = $cols;
    }

    public function getRows()
    {
        // TODO: Implement getRows() method.
    }

    public function getCols()
    {
        // TODO: Implement getCols() method.
    }
}
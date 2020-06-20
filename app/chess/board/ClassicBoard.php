<?php

namespace app\chess\board;

use app\chess\Constants;
use app\chess\exceptions\OutOfBoardException;
use app\chess\Objects;
use app\chess\pieces\Piece;

require_once "vendor/autoload.php";

class ClassicBoard extends AbstractBoard
{
    public function __construct()
    {
        parent::__construct(Constants::CLASSIC_BOARD_ROWS, Constants::CLASSIC_BOARD_COLS);

    }
}
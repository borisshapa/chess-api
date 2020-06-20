<?php

namespace app\chess\pieces;

use app\chess\board\Board;
use app\chess\board\Position;
use app\chess\Color;

interface Piece
{
    public function getColor() : Color;

    public function move(Board $board, Position $position) : array;
}
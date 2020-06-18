<?php

namespace chess\pieces;

use chess\board\Board;
use chess\board\Position;

interface Piece
{
    public function getColor() : Color;

    public function move(Board $board, Position $position);
}
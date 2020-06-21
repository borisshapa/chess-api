<?php

namespace app\chess\pieces;

use app\chess\board\Board;
use app\chess\board\Position;
use app\chess\Color;
use app\chess\moves\Move;

interface Piece
{
    public function getColor(): Color;

    public function attackedMoves(Board $board, Position $position): array;

    public function normalMoves(Board $board, Position $position): array;

    public function possibleMoves(Board $board, Position $position): array;
}
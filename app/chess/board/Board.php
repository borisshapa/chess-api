<?php

namespace app\chess\board;

use app\chess\Color;
use app\chess\moves\Move;
use app\chess\pieces\Piece;

interface Board
{
    public function getRows();

    public function getCols();

    public function addPiece(Piece $piece, Position $position);

    public function removePiece(Position $position);

    public function getPiece(Position $position);

    public function move(Piece $piece, Move $move);

    public function findPiece(string $piece, Color $color);

    public function neverMovesAtPosition(Position $position): bool;

    public function isPositionValid(Position $position) : bool;

    public function isPositionFree(Position $position) : bool;

    public function isPositionOccupied(Position $position) : bool;

    public function isPositionUnderAttack(Position $position, Color $color) : bool;
}

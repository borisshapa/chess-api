<?php

namespace app\chess\board;

use app\chess\Color;
use app\chess\moves\Move;
use app\chess\pieces\Piece;

interface Board
{
    public function getRows(): int;

    public function getCols() : int;

    public function addPiece(Piece $piece, Position $position) : void;

    public function removePiece(Position $position) : void;

    public function getPiece(Position $position) : ?Piece;

    public function move(Piece $piece, Move $move) : void;

    public function findPiece(string $piece, Color $color) : ?Position;

    public function pieceNeverMovedFromPosition(Position $position): bool;

    public function isPositionValid(Position $position) : bool;

    public function isPositionFree(Position $position) : bool;

    public function isPositionOccupied(Position $position) : bool;

    public function isPositionUnderAttack(Position $position, Color $color) : bool;
}

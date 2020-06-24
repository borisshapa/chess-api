<?php


namespace app\chess\pieces;

use app\chess\board\Board;
use app\chess\board\Position;
use app\chess\Color;
use utils\IntPair;

class Pawn extends AbstractPiece
{
    private static ?array $SINGLE_MOVES = null;

    public function __construct(Color $color)
    {
        parent::__construct($color);
        if (self::$SINGLE_MOVES == null) {
            self::$SINGLE_MOVES = array(
                new IntPair(2, 0),
                new IntPair(1, -1),
                new IntPair(1, 0),
                new IntPair(1, 1));
        }
    }

    public function attackedMoves(Board $board, Position $position): array
    {
        $color = $this->getColor();
        $direction = $this->getColor()->getDirection();

        $possibleMoves = array();
        foreach (self::$SINGLE_MOVES as $move) {
            if ($move->getSecond() != 0) {
                $row = $position->getRow() + $move->getFirst() * $direction;
                $col = $position->getCol() + $move->getSecond();
                $attackedPosition = new Position($row, $col);
                if (self::isPositionValidAndOccupied($board, $attackedPosition)) {
                    $attackedPiece = $board->getPiece($attackedPosition);
                    if (!self::checkColor($attackedPiece, $color)) {
                        array_push($possibleMoves, $attackedPosition);
                    }
                }
            }
        }
        return $possibleMoves;
    }

    public function normalMoves(Board $board, Position $position): array
    {
        $direction = $this->getColor()->getDirection();

        $possibleMoves = array();
        foreach (self::$SINGLE_MOVES as $move) {
            if ($move->getSecond() == 0) {
                $row = $position->getRow() + $move->getFirst() * $direction;
                $col = $position->getCol() + $move->getSecond();
                $newPosition = new Position($row, $col);
                if (self::isPositionValidAndFree($board, $newPosition)) {
                    if ($move->getFirst() == 2) {
                        $intermediatePosition = new Position($position->getRow() + $direction, $col);
                        if (self::isPositionValidAndFree($board, $intermediatePosition)
                            && $board->pieceNeverMovedFromPosition($position)) {
                            array_push($possibleMoves, $newPosition);
                        }
                    } else {
                        array_push($possibleMoves, $newPosition);
                    }
                }
            }
        }
        return $possibleMoves;
    }

    public
    function __toString(): string
    {
        return $this->toStr("P");
    }
}
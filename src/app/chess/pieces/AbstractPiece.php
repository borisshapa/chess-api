<?php

namespace app\chess\pieces;

use app\chess\exceptions\OutOfBoardException;
use app\chess\board\{
    Board,
    Position
};

use app\chess\Color;

abstract class AbstractPiece implements Piece
{
    protected Color $color;

    public function __construct(Color $color)
    {
        $this->color = $color;
    }

    public function getColor(): Color
    {
        return $this->color;
    }

    protected function toStr($letter): string
    {
        return $this->getColor()->getName()[0] . $letter;
    }

    public function possibleMoves(Board $board, Position $position): array
    {
        return array_merge($this->attackedMoves($board, $position),
            $this->normalMoves($board, $position));
    }

    protected static function checkColor(Piece $piece, Color $color)
    {
        return $piece->getColor() === $color;
    }

    protected function longNormalMoves(Board $board, Position $position, array $singleMoves): array
    {
        $possibleMoves = array();
        foreach ($singleMoves as $move) {
            $rowOffset = $move->getFirst();
            $colOffset = $move->getSecond();

            $newPosition = new Position($position->getRow() + $rowOffset,
                $position->getCol() + $colOffset);
            while ($this->isPositionValidAndFree($board, $newPosition)) {
                array_push($possibleMoves, $newPosition);
                $newPosition = new Position($newPosition->getRow() + $rowOffset,
                    $newPosition->getCol() + $colOffset);
            }
        }
        return $possibleMoves;
    }

    protected function longAttackMoves(Board $board, Position $position, array $singleMoves): array
    {
        $color = $this->getColor();
        $possibleMoves = array();
        foreach ($singleMoves as $move) {
            $rowOffset = $move->getFirst();
            $colOffset = $move->getSecond();

            $newPosition = new Position($position->getRow() + $rowOffset,
                $position->getCol() + $colOffset);
            while ($board->isPositionValid($newPosition)) {
                if (self::isPositionValidAndOccupied($board, $newPosition)) {
                    $attackedPiece = $board->getPiece($newPosition);
                    if (!$this->checkColor($attackedPiece, $color)) {
                        array_push($possibleMoves, $newPosition);
                    }
                    break;
                }
                $newPosition = new Position($newPosition->getRow() + $rowOffset,
                    $newPosition->getCol() + $colOffset);
            }
        }
        return $possibleMoves;
    }

    protected function singleAttackMoves(Board $board, Position $position, array $singleMoves): array
    {
        $color = $this->getColor();
        $possibleMoves = array();
        foreach ($singleMoves as $move) {
            $row = $position->getRow() + $move->getFirst();
            $col = $position->getCol() + $move->getSecond();
            $attackedPosition = new Position($row, $col);
            if (self::isPositionValidAndOccupied($board, $attackedPosition)) {
                $attackedPiece = $board->getPiece($attackedPosition);
                if (!self::checkColor($attackedPiece, $color)) {
                    array_push($possibleMoves, $attackedPosition);
                }
            }
        }
        return $possibleMoves;
    }

    protected function singleNormalMoves(Board $board, Position $position, array $singleMoves): array
    {
        $possibleMoves = array();
        foreach ($singleMoves as $move) {
            $row = $position->getRow() + $move->getFirst();
            $col = $position->getCol() + $move->getSecond();
            $newPosition = new Position($row, $col);
            if (self::isPositionValidAndFree($board, $newPosition)) {
                array_push($possibleMoves, $newPosition);
            }
        }
        return $possibleMoves;
    }

    protected function isPositionValidAndFree(Board $board, Position $position): bool
    {
        try {
            return $board->isPositionFree($position);
        } catch (OutOfBoardException $e) {
            return false;
        }
    }

    protected static function isPositionValidAndOccupied(Board $board, Position $position): bool
    {
        try {
            return $board->isPositionOccupied($position);
        } catch (OutOfBoardException $e) {
            return false;
        }
    }
}
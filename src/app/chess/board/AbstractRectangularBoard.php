<?php

namespace app\chess\board;

use app\chess\Color;
use app\chess\exceptions\InvalidMoveException;
use app\chess\exceptions\OutOfBoardException;
use app\chess\moves\Move;
use app\chess\pieces\Piece;
use app\chess\pieces\King;

abstract class AbstractRectangularBoard implements Board
{
    protected int $rows;
    protected int $cols;
    protected array $board;
    protected array $neverMovedFromPosition = array();

    protected function __construct(int $rows,
                                   int $cols)
    {
        $this->rows = $rows;
        $this->cols = $cols;
        for ($row = 0; $row < $rows; $row++) {
            for ($col = 0; $col < $cols; $col++) {
                $this->board[$row][$col] = null;
            }
        }
    }

    public function getRows(): int
    {
        return $this->rows;
    }

    public function getCols(): int
    {
        return $this->cols;
    }


    public function addPiece(Piece $piece, Position $position): void
    {
        if (!$this->isPositionValid($position)) {
            throw new OutOfBoardException($this->getRows(), $this->getCols(), $position);
        }
        $this->board[$position->getRow()][$position->getCol()] = $piece;
        array_push($this->neverMovedFromPosition, $position);
    }

    public function removePiece(Position $position): void
    {
        if (!$this->isPositionValid($position)) {
            throw new OutOfBoardException($this->getRows(), $this->getCols(), $position);
        }
        $this->board[$position->getRow()][$position->getCol()] = null;

        $deletedKey = array_search($position, $this->neverMovedFromPosition);
        if ($deletedKey) {
            unset($this->neverMovedFromPosition[$deletedKey]);
        }
    }

    public function isPositionValid(Position $position): bool
    {
        return $position->getRow() >= 0 && $position->getRow() < $this->getRows()
            && $position->getCol() >= 0 && $position->getCol() < $this->getCols();
    }

    public function isPositionFree(Position $position): bool
    {
        $piece = $this->getPiece($position);
        return !isset($piece);
    }

    public function isPositionOccupied(Position $position): bool
    {
        $piece = $this->getPiece($position);
        return isset($piece);
    }

    public function getPiece(Position $position): ?Piece
    {
        if (!$this->isPositionValid($position)) {
            throw new OutOfBoardException($this->getRows(), $this->getCols(), $position);
        }
        return $this->board[$position->getRow()][$position->getCol()] ?? null;
    }

    public function move(Piece $piece, Move $move): void
    {
        $from = $move->getFrom();
        $to = $move->getTo();

        if (!$this->isPositionOccupied($from)) {
            throw new InvalidMoveException("There is no piece in position ({$from->getRow()}, {$from->getCol()})");
        }
        if ($this->getPiece($from) !== $piece) {
            throw new InvalidMoveException("The moving piece does not match the piece in position ({$from->getRow()},{$from->getCol()})");
        }

        $this->removePiece($from);

        $this->board[$to->getRow()][$to->getCol()] = $piece;
    }

    public function isPositionUnderAttack(Position $position, Color $color): bool
    {
        $board = clone $this;
        $board->addPiece(new King($color), $position);
        for ($row = 0; $row < $board->getRows(); $row++) {
            for ($col = 0; $col < $board->getCols(); $col++) {
                $rivalPosition = new Position($row, $col);
                if ($board->isPositionOccupied($rivalPosition)) {
                    $piece = $board->getPiece($rivalPosition);
                    if ($piece->getColor() !== $color) {
                        $attackedPositions = $piece->attackedMoves($board, $rivalPosition);
                        if (in_array($position, $attackedPositions)) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function pieceNeverMovedFromPosition(Position $position): bool
    {
        if (!$this->isPositionValid($position)) {
            throw new OutOfBoardException($this->getRows(), $this->getCols(), $position);
        }
        return in_array($position, $this->neverMovedFromPosition);
    }

    public function findPiece(string $pieceClass, Color $color): ?Position
    {
        for ($row = 0; $row < $this->getRows(); $row++) {
            for ($col = 0; $col < $this->getCols(); $col++) {
                $pos = new Position($row, $col);
                if ($this->isPositionOccupied($pos)) {
                    $piece = $this->getPiece($pos);
                    if ($piece->getColor() == $color && $piece instanceof $pieceClass) {
                        return $pos;
                    }
                }
            }
        }
        return null;
    }
}
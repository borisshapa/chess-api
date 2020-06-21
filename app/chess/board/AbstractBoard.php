<?php

namespace app\chess\board;

use app\chess\Color;
use app\chess\Constants;
use app\chess\exceptions\GameException;
use app\chess\exceptions\OutOfBoardException;
use app\chess\moves\Move;
use app\chess\Objects;
use app\chess\pieces\Piece;
use app\chess\pieces\King;

require_once "app/chess/pieces/Pieces.php";
require_once "vendor/autoload.php";

abstract class AbstractBoard implements Board
{
    private $rows;
    private $cols;
    private $board;
    private $neverMoves = array();

    protected function __construct(int $rows,
                                   int $cols)
    {
        $this->rows = $rows;
        $this->cols = $cols;
        foreach (range(1, $rows) as $row) {
            foreach (range(1, $cols) as $col) {
                $this->board[$row][$col] = null;
            }
        }
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function getCols()
    {
        return $this->cols;
    }


    public function addPiece(Piece $piece, Position $position)
    {
        $this->board[$position->getRow()][$position->getCol()] = $piece;
        array_push($this->neverMoves, $position);
    }

    public function removePiece(Position $position)
    {
        $this->board[$position->getRow()][$position->getCol()] = null;
    }

    public function isPositionValid(Position $position): bool
    {
        return $position->getRow() >= 0 && $position->getRow() < $this->getRows()
            && $position->getCol() >= 0 && $position->getCol() < $this->getCols();
    }

    public function isPositionFree(Position $position): bool
    {
        if (!$this->isPositionValid($position)) {
            return false;
        }
        $piece = $this->getPiece($position);
        var_dump($piece);
        return !isset($piece);
    }

    public function isPositionOccupied(Position $position): bool
    {
        if (!$this->isPositionValid($position)) {
            return false;
        }
        $piece = $this->getPiece($position);
        return isset($piece);
    }

    public function getPiece(Position $position)
    {
        if (!$this->isPositionValid($position)) {
            throw new \Exception("THINK ABOUT IT");
        }
        return $this->board[$position->getRow()][$position->getCol()];
    }

    public function move(Piece $piece, Move $move)
    {
        $from = $move->getFrom();
        if (!$this->isPositionOccupied($from)) {
            throw new \Exception("THINK ABOUT IT");
        }
        $this->board[$from->getRow()][$from->getCol()] = null;

        $to = $move->getTo();
        $this->board[$to->getRow()][$to->getCol()] = $piece;

        $deletedKey = array_search($from, $this->neverMoves);
        unset($this->neverMoves[$deletedKey]);
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
                    if ($piece->getColor() != $color) {
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

    public function neverMovesAtPosition(Position $position): bool
    {
        return in_array($position, $this->neverMoves);
    }

    public function findPiece(string $pieceClass, Color $color)
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
        throw new GameException("Piese dont dound");
    }
}
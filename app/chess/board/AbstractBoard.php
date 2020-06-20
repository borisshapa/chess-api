<?php

namespace app\chess\board;

use app\chess\Constants;
use app\chess\exceptions\OutOfBoardException;
use app\chess\Objects;
use app\chess\pieces\Piece;

require_once "vendor/autoload.php";

abstract class AbstractBoard implements Board
{
    private $rows;
    private $cols;
    private $board;

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
        Objects::requireNonNull($piece, Constants::NULL_PIECE_ERROR_MESSAGE);
        $this->checkPosition($position);
        $arrayRow = $this->getArrayRow($position->getRow());
        $arrayCol = $position->getCol();
        $this->board[$arrayRow][$arrayCol] = $piece;
    }

    public function removePiece(Position $position)
    {
        $this->checkPosition($position);
        $arrayRow = $this->getArrayRow($position->getRow());
        $arrayCol = $position->getCol();
        $this->board[$arrayRow][$arrayCol] = null;
    }

    private function getArrayRow(int $boardRow): int
    {
        return $this->getRows() - $boardRow - 1;
    }

    public function checkPosition(Position $position)
    {
        if ($position->getRow() < 0 || $position->getRow() >= $this->getRows()
            || $position->getCol() < 0 || $position->getCol() >= $this->getRows()) {
            throw new OutOfBoardException("Row: " . $position->getRow() . ", col: " . $position->getCol());
        }
    }

    public function getPiece(Position $position): Piece
    {
        $arrayRow = $this->getArrayRow($position->getRow());
        $arrayCol = $position->getCol();
        return $this->board[$arrayRow][$arrayCol];
    }
}
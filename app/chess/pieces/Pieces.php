<?php

namespace app\chess\pieces;

use app\chess\Color;
use app\chess\exceptions\GameException;
use app\chess\game\ClassicGame;
use app\chess\moves\Move;
use app\chess\board\{Board, Pair, Position};

require_once "vendor/autoload.php";

class Pawn extends AbstractPiece
{
    private static $SINGLE_MOVES = null;

    private static function init()
    {
        if (self::$SINGLE_MOVES == null) {
            self::$SINGLE_MOVES = array(
                new Pair(2, 0),
                new Pair(1, -1),
                new Pair(1, 0),
                new Pair(1, 1));
        }
    }

    public function attackedMoves(Board $board, Position $position): array
    {
        self::init();
        $color = $this->getColor();
        $direction = $this->getColor()->getDirection();

        $possibleMoves = array();
        foreach (self::$SINGLE_MOVES as $move) {
            if ($move->getSecond() != 0) {
                $row = $position->getRow() + $move->getFirst() * $direction;
                $col = $position->getCol() + $move->getSecond();
                $attackedPosition = new Position($row, $col);
                if ($board->isPositionOccupied($attackedPosition)) {
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
        self::init();
        $direction = $this->getColor()->getDirection();

        $possibleMoves = array();
        foreach (self::$SINGLE_MOVES as $move) {
            if ($move->getSecond() == 0) {
                $row = $position->getRow() + $move->getFirst() * $direction;
                $col = $position->getCol() + $move->getSecond();
                $newPosition = new Position($row, $col);
                if ($board->isPositionFree($newPosition)) {
                    if ($move->getFirst() == 2) {
                        $intermediatePosition = new Position($position->getRow() + $direction, $col);
                        if ($board->isPositionFree($intermediatePosition) && $board->neverMovesAtPosition($position)) {
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

class Knight extends AbstractPiece
{
    private static $SINGLE_MOVES = null;

    private static function init() {
        if (self::$SINGLE_MOVES == null) {
            self::$SINGLE_MOVES = array(
                new Pair(2, 1), new Pair(2, -1), new Pair(1, 2), new Pair(1, -2),
                new Pair(-2, 1), new Pair(-2, -1), new Pair(-1, 2), new Pair(-1, -2));
        }
    }

    public function attackedMoves(Board $board, Position $position): array
    {
        self::init();
        return $this->singleAttackMoves($board, $position, self::$SINGLE_MOVES);
    }

    public function normalMoves(Board $board, Position $position): array
    {
        self::init();
        return $this->singleNormalMoves($board, $position, self::$SINGLE_MOVES);
    }

    public function __toString(): string
    {
        return $this->toStr("N");
    }
}

class Bishop extends AbstractPiece
{
    private static $SINGLE_MOVES = null;

    private static function init()
    {
        if (self::$SINGLE_MOVES == null) {
            self::$SINGLE_MOVES = array(
                new Pair(1, 1), new Pair(1, -1),
                new Pair(-1, 1), new Pair(-1, -1)
            );
        }
    }

    public function __toString(): string
    {
        return $this->toStr("B");
    }

    public function attackedMoves(Board $board, Position $position): array
    {
        self::init();
        return $this->longAttackMoves($board, $position, self::$SINGLE_MOVES);
    }

    public function normalMoves(Board $board, Position $position): array
    {
        self::init();
        return $this->longNormalMoves($board, $position, self::$SINGLE_MOVES);
    }
}

class Rook extends AbstractPiece
{
    private static $SINGLE_MOVES = null;

    private static function init() {
        if (self::$SINGLE_MOVES == null) {
            self::$SINGLE_MOVES = array(
                new Pair(1, 0), new Pair(-1, 0),
                new Pair(0, 1), new Pair(0, -1)
            );
        }
    }

    public function __toString(): string
    {
        return $this->toStr("R");
    }

    public function attackedMoves(Board $board, Position $position): array
    {
        self::init();
        return $this->longAttackMoves($board, $position, self::$SINGLE_MOVES);
    }

    public function normalMoves(Board $board, Position $position): array
    {
        self::init();
        return $this->longNormalMoves($board, $position, self::$SINGLE_MOVES);
    }
}

class Queen extends AbstractPiece
{
    public function __toString(): string
    {
        return $this->toStr("Q");
    }

    public function attackedMoves(Board $board, Position $position): array
    {
        $rook = new Rook($this->color);
        $bishop = new Bishop($this->color);
        return array_merge($rook->attackedMoves($board, $position),
            $bishop->attackedMoves($board, $position));
    }

    public function normalMoves(Board $board, Position $position): array
    {
        $rook = new Rook($this->color);
        $bishop = new Bishop($this->color);
        return array_merge($rook->normalMoves($board, $position),
            $bishop->normalMoves($board, $position));
    }
}

class King extends AbstractPiece
{
    private static $SINGLE_MOVES = null;

    private static function init() {
        if (self::$SINGLE_MOVES == null) {
            self::$SINGLE_MOVES = array(
                new Pair(1, 0), new Pair(1, 1), new Pair(1, -1), new Pair(0, 1),
                new Pair(0, -1), new Pair(-1, 1), new Pair(-1, 0), new Pair(-1, -1)
            );
        }
    }

    public function __toString(): string
    {
        return $this->toStr("K");
    }

    private function removeDangerousMoves(array &$possibleMoves, Board $board)
    {
        for ($i = 0; $i < count($possibleMoves); $i++) {
            $newPosition = $possibleMoves[$i];
            if ($board->isPositionUnderAttack($newPosition, $this->getColor())) {
                unset($possibleMoves[$i]);
            }
        }
    }

    public function attackedMoves(Board $board, Position $position): array
    {
        self::init();
        $possibleMoves = $this->singleAttackMoves($board, $position, self::$SINGLE_MOVES);
        $this->removeDangerousMoves($possibleMoves, $board);
        return $possibleMoves;
    }

    public function normalMoves(Board $board, Position $position): array
    {
        self::init();
        $possibleMoves = $this->singleNormalMoves($board, $position, self::$SINGLE_MOVES);
        $this->removeDangerousMoves($possibleMoves, $board);
        return $possibleMoves;
    }
}
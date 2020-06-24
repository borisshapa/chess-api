<?php


namespace app\chess\pieces;


use app\chess\board\Board;
use app\chess\board\Position;
use app\chess\Color;
use utils\Pair;

class Bishop extends AbstractPiece
{
    private static ?array $SINGLE_MOVES = null;

    public function __toString(): string
    {
        return $this->toStr("B");
    }

    public function attackedMoves(Board $board, Position $position): array
    {
        return $this->longAttackMoves($board, $position, self::$SINGLE_MOVES);
    }

    public function normalMoves(Board $board, Position $position): array
    {
        return $this->longNormalMoves($board, $position, self::$SINGLE_MOVES);
    }

    public static function init(): void
    {
        if (self::$SINGLE_MOVES == null) {
            self::$SINGLE_MOVES = array(
                new Pair(1, 1),
                new Pair(1, -1),
                new Pair(-1, 1),
                new Pair(-1, -1)
            );
        }
    }
}

Bishop::init();
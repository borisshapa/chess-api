<?php


namespace app\chess\pieces;


use app\chess\board\Board;
use app\chess\board\Position;
use app\chess\Color;
use utils\IntPair;

class Knight extends AbstractPiece
{
    private static ?array $SINGLE_MOVES = null;

    public function __construct(Color $color)
    {
        parent::__construct($color);
        if (self::$SINGLE_MOVES == null) {
            self::$SINGLE_MOVES = array(
                new IntPair(2, 1), new IntPair(2, -1), new IntPair(1, 2), new IntPair(1, -2),
                new IntPair(-2, 1), new IntPair(-2, -1), new IntPair(-1, 2), new IntPair(-1, -2));
        }
    }

    public function attackedMoves(Board $board, Position $position): array
    {
        return $this->singleAttackMoves($board, $position, self::$SINGLE_MOVES);
    }

    public function normalMoves(Board $board, Position $position): array
    {
        return $this->singleNormalMoves($board, $position, self::$SINGLE_MOVES);
    }

    public function __toString(): string
    {
        return $this->toStr("N");
    }
}
<?php


namespace app\chess\pieces;


use app\chess\board\Board;
use app\chess\board\Position;
use app\chess\Color;
use utils\IntPair;

class Rook extends AbstractPiece implements Piece
{
    private static ?array $SINGLE_MOVES = null;

    public function __construct(Color $color)
    {
        parent::__construct($color);
        if (self::$SINGLE_MOVES == null) {
            self::$SINGLE_MOVES = array(
                new IntPair(1, 0),
                new IntPair(-1, 0),
                new IntPair(0, 1),
                new IntPair(0, -1)
            );
        }
    }

    public function __toString(): string
    {
        return $this->toStr("R");
    }

    public function attackedMoves(Board $board, Position $position): array
    {
        return $this->longAttackMoves($board, $position, self::$SINGLE_MOVES);
    }

    public function normalMoves(Board $board, Position $position): array
    {
        return $this->longNormalMoves($board, $position, self::$SINGLE_MOVES);
    }
}
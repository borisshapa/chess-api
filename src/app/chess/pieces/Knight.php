<?php


namespace app\chess\pieces;


use app\chess\board\Board;
use app\chess\board\Position;
use utils\Pair;

/**
 * Class Knight
 * @package app\chess\pieces
 * @see Piece
 * @see AbstractPiece
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class Knight extends AbstractPiece
{
    private static ?array $SINGLE_MOVES = null;

    /**
     * Initialization of a static array that contains
     * the minimum possible displacements of the piece
     * in the form of a two-dimensional vector.
     */
    public static function init(): void
    {
        if (self::$SINGLE_MOVES == null) {
            self::$SINGLE_MOVES = array(
                new Pair(2, 1), new Pair(2, -1), new Pair(1, 2), new Pair(1, -2),
                new Pair(-2, 1), new Pair(-2, -1), new Pair(-1, 2), new Pair(-1, -2));
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

    /**
     * @return string string representation of the knight ({@example "WN" — white knight}).
     */
    public function __toString(): string
    {
        return $this->toStr("N");
    }
}

Knight::init();
<?php


namespace app\chess\pieces;


use app\chess\board\Board;
use app\chess\board\Position;
use app\chess\Color;
use utils\Pair;

/**
 * Class Rook
 * @package app\chess\pieces
 * @see Piece
 * @see AbstractPiece
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class Rook extends AbstractPiece
{
    private static ?array $SINGLE_MOVES = null;

    /**
     * Rook constructor.
     * @param Color $color piece color
     */
    public function __construct(Color $color)
    {
        parent::__construct($color, "R");
    }

    /**
     * Initialization of a static array that contains
     * the minimum possible displacements of the piece
     * in the form of a two-dimensional vector.
     */
    public static function init(): void
    {
        if (self::$SINGLE_MOVES == null) {
            self::$SINGLE_MOVES = array(
                new Pair(1, 0),
                new Pair(-1, 0),
                new Pair(0, 1),
                new Pair(0, -1)
            );
        }
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

Rook::init();
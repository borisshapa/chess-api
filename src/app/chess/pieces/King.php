<?php


namespace app\chess\pieces;

use app\chess\board\Board;
use app\chess\board\Position;
use app\chess\Color;
use utils\Pair;

/**
 * Class King
 * @package app\chess\pieces
 * @see Piece
 * @see AbstractPiece
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class King extends AbstractPiece
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
                new Pair(1, 0), new Pair(1, 1), new Pair(1, -1), new Pair(0, 1),
                new Pair(0, -1), new Pair(-1, 1), new Pair(-1, 0), new Pair(-1, -1)
            );
        }
    }

    /**
     * King constructor.
     * @param Color $color piece color
     */
    public function __construct(Color $color)
    {
        parent::__construct($color, "K");
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
        $possibleMoves = $this->singleAttackMoves($board, $position, self::$SINGLE_MOVES);
        $this->removeDangerousMoves($possibleMoves, $board);
        return $possibleMoves;
    }

    public function normalMoves(Board $board, Position $position): array
    {
        $possibleMoves = $this->singleNormalMoves($board, $position, self::$SINGLE_MOVES);
        $this->removeDangerousMoves($possibleMoves, $board);
        return $possibleMoves;
    }
}

King::init();
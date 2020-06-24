<?php


namespace app\chess\pieces;

use app\chess\board\Board;
use app\chess\board\Position;
use app\chess\Color;
use utils\IntPair;

class King extends AbstractPiece
{
    private static ?array $SINGLE_MOVES = null;

    public function __construct(Color $color)
    {
        parent::__construct($color);
        if (self::$SINGLE_MOVES == null) {
            self::$SINGLE_MOVES = array(
                new IntPair(1, 0), new IntPair(1, 1), new IntPair(1, -1), new IntPair(0, 1),
                new IntPair(0, -1), new IntPair(-1, 1), new IntPair(-1, 0), new IntPair(-1, -1)
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
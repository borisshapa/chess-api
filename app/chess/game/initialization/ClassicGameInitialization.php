<?php


namespace app\chess\game\initialization;

use app\chess\board\Board;
use app\chess\board\Position;
use app\chess\exceptions\GameException;
use app\chess\pieces\Bishop;
use app\chess\pieces\King;
use app\chess\pieces\Knight;
use app\chess\pieces\Pawn;
use app\chess\pieces\Piece;
use app\chess\pieces\Queen;
use app\chess\pieces\Rook;
use app\chess\players\Player;

require_once "app/chess/pieces/Pieces.php";

class ClassicGameInitialization implements GameInitialization
{
    private const PLAYERS = 2;
    private const BOARD_SIDE = 8;

    private const PIECES_ORDER = array(
        Rook::class,
        Knight::class,
        Bishop::class,
        Queen::class,
        King::class,
        Bishop::class,
        Knight::class,
        Rook::class
    );

    public function initialize(array $players, Board $board)
    {
        if (count($players) != self::PLAYERS) {
            throw new GameException("Expected 2 players, found: " . count($players));
        }

        if ($board->getRows() != self::BOARD_SIDE || $board->getCols() != self::BOARD_SIDE) {
            throw new \Exception("Expected 8x8 board");
        }

        $player1 = $players[0];
        $player2 = $players[1];

        $this->addValuablePieces($player1, $board);
        $this->addValuablePieces($player2, $board);

        $this->addPawns($player1, $board);
        $this->addPawns($player2, $board);
    }

    private function addValuablePieces(Player &$player, Board &$board)
    {
        for ($col = 0; $col < self::BOARD_SIDE; $col++) {
            $pieceType = self::PIECES_ORDER[$col];
            $piece = new $pieceType($player->getColor());
            $this->addPiece($player, $board, $piece, $col, array(0, 7));
        }
    }

    private function addPawns(Player &$player, Board $board)
    {
        for ($col = 0; $col < self::BOARD_SIDE; $col++) {
            $pawn = new Pawn($player->getColor());
            $this->addPiece($player, $board, $pawn, $col, array(1, 6));
        }
    }

    private function addPiece(Player &$player, Board &$board, Piece &$piece, int $col, array $rows)
    {
        if (count($rows) < 2) {
            throw new GameException("Expected two rows of pieces, found: " . count($rows));
        }
        $playerColor = $player->getColor();
//        $player->addPiece($piece);
        $board->addPiece($piece, new Position($playerColor->getName() == "Black" ? $rows[0] : $rows[1], $col));
    }
}
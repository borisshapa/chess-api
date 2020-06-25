<?php


namespace app\chess\game\initialization;

use app\chess\board\Board;
use app\chess\board\Position;
use app\chess\Color;
use app\chess\exceptions\GameInitializationException;
use app\chess\pieces\Bishop;
use app\chess\pieces\King;
use app\chess\pieces\Knight;
use app\chess\pieces\Pawn;
use app\chess\pieces\Piece;
use app\chess\pieces\Queen;
use app\chess\pieces\Rook;
use app\chess\players\Player;

/**
 * Class ClassicGameInitialization
 * The classic arrangement of pieces on the board ({@link https://en.wikipedia.org/wiki/Chess#Setup}).
 * @package app\chess\game\initialization
 * @see GameInitialization
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class ClassicGameInitialization implements GameInitialization
{
    private static ?GameInitialization $instance = null;
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

    /**
     * ClassicGameInitialization constructor.
     * Prevent constructor calls outside the class.
     */
    private function __construct()
    {
    }

    /**
     * Restriction on cloning of an object of a class.
     */
    protected function __clone()
    {
    }

    public static function getInstance(): GameInitialization
    {
        if (!isset(self::$instance)) {
            self::$instance = new ClassicGameInitialization();
        }
        return self::$instance;
    }

    /**
     * @inheritDoc
     * @throws GameInitializationException if the size of the passed
     * array of players is not 2 or the size of the board is not 8 x 8.
     */
    public function initialize(array $players, Board $board): void
    {
        if (count($players) != self::PLAYERS) {
            throw new GameInitializationException("Expected 2 players, found: " . count($players));
        }

        if ($board->getRows() != self::BOARD_SIDE || $board->getCols() != self::BOARD_SIDE) {
            throw new GameInitializationException("Expected 8x8 board");
        }

        foreach ($players as $player) {
            $this->addValuablePieces($player, $board);
            $this->addPawns($player, $board);
        }
    }

    /**
     * Adds rooks, bishops, knights, queen and king to the board.
     * @param Player $player for whom to add pieces
     * @param Board $board where to add pieces
     * @throws GameInitializationException if not two possible rows for arrangement
     * of pieces are passed to the function {@see ClassicGameInitialization::addPiece()}.
     */
    private function addValuablePieces(Player $player, Board $board): void
    {
        for ($col = 0; $col < self::BOARD_SIDE; $col++) {
            $pieceType = self::PIECES_ORDER[$col];
            $piece = new $pieceType($player->getColor());
            $this->addPiece($player, $board, $piece, $col, array(0, 7));
        }
    }

    /**
     * Adds pawns to the board.
     * @param Player $player for whom to add pieces
     * @param Board $board where to add pieces
     * @throws GameInitializationException if not two possible rows for arrangement
     * of pawns are passed to the function {@see ClassicGameInitialization::addPiece()}.
     */
    private function addPawns(Player $player, Board $board): void
    {
        for ($col = 0; $col < self::BOARD_SIDE; $col++) {
            $pawn = new Pawn($player->getColor());
            $this->addPiece($player, $board, $pawn, $col, array(1, 6));
        }
    }

    /**
     * Adds a piece to the board.
     * @param Player $player for whom to add piece
     * @param Board $board where to add pieces
     * @param Piece $piece what {@see Piece} to add
     * @param int $col piece column
     * @param array $rows A set of possible rows of a piece depending on its color.
     * - <var>$rows[0]</var> row for black pieces
     * - <var>$rows[1]</var> row for white pieces
     * @throws GameInitializationException if the number of possible rows for setting the piece is not equal to two.
     */
    private function addPiece(Player $player, Board $board, Piece $piece, int $col, array $rows): void
    {
        if (count($rows) != 2) {
            throw new GameInitializationException("Expected 2 rows of pieces, found: " . count($rows));
        }
        $board->addPiece($piece, new Position($player->getColor() === Color::getBlack() ? $rows[0] : $rows[1], $col));
    }
}
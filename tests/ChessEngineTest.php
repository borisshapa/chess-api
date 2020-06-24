<?php

namespace tests;

use app\chess\board\Board;
use app\chess\board\Position;
use app\chess\Color;
use app\chess\exceptions\InvalidMoveException;
use app\chess\exceptions\OutOfBoardException;
use app\chess\game\ClassicGame;
use app\chess\game\Game;
use app\chess\game\initialization\ClassicGameInitialization;
use app\chess\game\initialization\GameInitialization;
use app\chess\moves\Move;
use app\chess\pieces\Bishop;
use app\chess\pieces\King;
use app\chess\pieces\Knight;
use app\chess\pieces\Pawn;
use app\chess\pieces\Queen;
use app\chess\pieces\Rook;
use app\chess\players\ClassicPlayer;
use app\chess\players\Player;
use phpDocumentor\Reflection\DocBlock\Tags\See;
use phpDocumentor\Reflection\Types\Self_;
use phpDocumentor\Reflection\Types\This;
use PHPUnit\Framework\TestCase;

class ChessEngineTest extends TestCase
{
    private static Player $player1;
    private static Player $player2;
    private static Move $e2e4Move;
    private static GameInitialization $classicGameInitialization;

    public static function setUpBeforeClass(): void
    {
        self::$player1 = new ClassicPlayer("Bob", Color::getWhite());
        self::$player2 = new ClassicPlayer("Alice", Color::getBlack());
        self::$e2e4Move = self::getMove(6, 4, 4, 4);
        self::$classicGameInitialization = ClassicGameInitialization::getInstance();
    }

    private static function assertBoardsEquals(array $epxpectedBoard, Board $board): void
    {
        self::assertEquals(count($epxpectedBoard), $board->getRows());
        for ($row = 0; $row < $board->getRows(); $row++) {
            self::assertEquals(count($epxpectedBoard[$row]), $board->getCols());
            for ($col = 0; $col < $board->getCols(); $col++) {
                $piece = $board->getPiece(new Position($row, $col)) ?? null;
                $pieceStr = isset($piece) ? strval($piece) : "__";
                self::assertEquals($epxpectedBoard[$row][$col], $pieceStr);
            }
        }
    }

    private static function createClassicGame()
    {
        $game = new ClassicGame(self::$player1, self::$player2);
        $game->initialize(self::$classicGameInitialization);
        return $game;
    }

    private static function getMove($row1, $col1, $row2, $col2): Move
    {
        return new Move(new Position($row1, $col1), new Position($row2, $col2));
    }

    private static function chainOfMoves(Game $game, array $moves): void
    {
        foreach ($moves as $move) {
            $game->move($move);
        }
    }

    public static function testPlayers(): void
    {
        $game = self::createClassicGame();
        $game->move(self::$e2e4Move);

        self::assertContains(self::$player1, $game->getPlayers());
        self::assertContains(self::$player2, $game->getPlayers());
    }

    public static function testPawnMove(): void
    {
        $game = self::createClassicGame();
        $game->move(self::$e2e4Move);

        self::assertBoardsEquals(PAWN_MOVE_BOARD, $game->getBoard(),);
    }

    public static function testRookMove(): void
    {
        $game = self::createClassicGame();

        self::chainOfMoves($game,
            array(
                self::getMove(6, 7, 4, 7),
                self::getMove(1, 2, 2, 2),
                self::getMove(7, 7, 5, 7)
            ));

        self::assertBoardsEquals(ROOK_MOVE_BOARD, $game->getBoard(),);
    }

    public static function testKnightMove(): void
    {
        $game = self::createClassicGame();

        $game->move(self::getMove(7, 1, 5, 2));

        self::assertBoardsEquals(KNIGHT_MOVE_BOARD, $game->getBoard(),);
    }

    public static function testBishopMove(): void
    {
        $game = self::createClassicGame();

        self::chainOfMoves($game,
            array(
                self::getMove(7, 1, 5, 2),
                self::getMove(1, 3, 2, 3),
                self::getMove(6, 6, 4, 6),
                self::getMove(0, 2, 4, 6)
            ));

        self::assertBoardsEquals(BISHOP_MOVE_BOARD, $game->getBoard(),);
    }

    public static function testQueenMove(): void
    {
        $game = self::createClassicGame();

        self::chainOfMoves($game,
            array(
                self::$e2e4Move,
                self::getMove(1, 3, 3, 3),
                self::getMove(4, 4, 3, 3),
                self::getMove(0, 3, 3, 3)
            ));

        self::assertBoardsEquals(QUEEN_MOVE_BOARD, $game->getBoard());
    }

    public static function testKingMove(): void
    {
        $game = self::createClassicGame();

        self::chainOfMoves($game,
            array(
                self::getMove(6, 3, 4, 3),
                self::getMove(1, 3, 3, 3),
                self::getMove(7, 4, 6, 3)
            ));

        self::assertBoardsEquals(KING_MOVE_BOARD, $game->getBoard());
    }

    public static function testKidsCheckmate(): void
    {
        $game = self::createClassicGame();

        self::chainOfMoves($game,
            array(
                self::$e2e4Move,
                self::getMove(1, 4, 3, 4),
                self::getMove(7, 3, 3, 7),
                self::getMove(0, 1, 2, 2),
                self::getMove(7, 5, 4, 2),
                self::getMove(0, 6, 2, 5),
                self::getMove(3, 7, 1, 5)
            ));

        self::assertBoardsEquals(KIDS_CHECKMATE_BOARD, $game->getBoard());
        self::assertTrue($game->checkIfColorWon(Color::getWhite()));
    }

    public static function testEnPassant(): void
    {
        $game = self::createClassicGame();

        self::chainOfMoves($game,
            array(
                self::$e2e4Move,
                self::getMove(0, 6, 2, 5),
                self::getMove(4, 4, 3, 4),
                self::getMove(1, 3, 3, 3),
                self::getMove(3, 4, 2, 3)
            ));

        self::assertBoardsEquals(EN_PASSANT_BOARD, $game->getBoard());
    }

    public static function testShortCastling(): void
    {
        $game = self::createClassicGame();

        self::chainOfMoves($game,
            array(
                self::$e2e4Move,
                self::getMove(1, 4, 3, 4),
                self::getMove(7, 5, 4, 2),
                self::getMove(0, 1, 2, 2),
                self::getMove(7, 6, 5, 5),
                self::getMove(0, 6, 2, 5),
                self::getMove(7, 4, 7, 6)
            ));

        self::assertBoardsEquals(SHORT_CASTLING_BOARD, $game->getBoard());
    }

    public static function testLongCastling()
    {
        $game = self::createClassicGame();

        self::chainOfMoves($game,
            array(
                self::$e2e4Move,
                self::getMove(1, 3, 3, 3),
                self::getMove(7, 6, 5, 5),
                self::getMove(0, 2, 4, 6),
                self::getMove(7, 1, 5, 2),
                self::getMove(0, 1, 2, 2),
                self::getMove(5, 2, 3, 3),
                self::getMove(0, 3, 3, 3),
                self::getMove(4, 4, 3, 3),
                self::getMove(0, 4, 0, 2)
            ));

        self::assertBoardsEquals(LONG_CASTLING_BOARD, $game->getBoard());
    }

    public static function testPawnTransformation()
    {
        $game = self::createClassicGame();

        self::chainOfMoves($game,
            array(
                self::$e2e4Move,
                self::getMove(1, 5, 3, 5),
                self::getMove(6, 5, 4, 5),
                self::getMove(3, 5, 4, 4),
                self::getMove(4, 5, 3, 5),
                self::getMove(1, 4, 2, 4),
                self::getMove(3, 5, 2, 5),
                self::getMove(0, 5, 3, 2),
                self::getMove(2, 5, 1, 5),
                self::getMove(0, 4, 1, 4)
            ));
        $game->move(new Move(new Position(1, 5), new Position(0, 5)), Rook::class);
        self::chainOfMoves($game,
            array(
                self::getMove(0, 6, 2, 5),
                self::getMove(0, 5, 0, 3)
            ));

        self::assertBoardsEquals(PAWN_TRANSFORMATION_BOARD, $game->getBoard());
    }

    public function testMoveFromEmptySquare(): void
    {
        $this->expectException(InvalidMoveException::class);

        $game = self::createClassicGame();
        $game->move(self::getMove(4, 4, 3, 4));
    }

    public function testInvalidDiagonalPawnMove(): void
    {
        $this->expectException(InvalidMoveException::class);

        $game = self::createClassicGame();
        $game->move(self::getMove(6, 4, 5, 5));
    }

    public function testInvalidStraightPawnMove(): void
    {
        $this->expectException(InvalidMoveException::class);

        $game = self::createClassicGame();
        self::chainOfMoves($game,
            array(
                self::$e2e4Move,
                self::getMove(1, 4, 3, 4),
                self::getMove(4, 4, 3, 4)
            ));
    }

    public function testMoveThroughPiece(): void
    {
        $this->expectException(InvalidMoveException::class);

        $game = self::createClassicGame();
        $game->move(self::getMove(7, 3, 3, 7));
    }

    public function testInvalidBishopMove(): void
    {
        $this->expectException(InvalidMoveException::class);

        $game = self::createClassicGame();
        self::chainOfMoves($game,
            array(
                self::getMove(6, 5, 4, 5),
                self::getMove(1, 5, 3, 5),
                self::getMove(7, 5, 5, 5)
            ));
    }

    public function testEnPassantInappropriatePawn(): void
    {
        $this->expectException(InvalidMoveException::class);

        $game = self::createClassicGame();
        self::chainOfMoves($game,
            array(
                self::$e2e4Move,
                self::getMove(1, 3, 3, 3),
                self::getMove(7, 5, 4, 2),
                self::getMove(3, 3, 4, 3),
                self::getMove(4, 4, 3, 3)
            ));
    }

    public function testEnPassantNotRightAway(): void
    {
        $this->expectException(InvalidMoveException::class);

        $game = self::createClassicGame();
        self::chainOfMoves($game,
            array(
                self::$e2e4Move,
                self::getMove(0, 6, 2, 5),
                self::getMove(4, 4, 3, 4),
                self::getMove(1, 3, 3, 3),
                self::getMove(6, 0, 5, 0),
                self::getMove(1, 0, 2, 0),
                self::getMove(3, 4, 2, 3)
            ));
    }

    public function testCastlingWayUnderAttack(): void
    {
        $this->expectException(InvalidMoveException::class);

        $game = self::createClassicGame();
        self::chainOfMoves($game,
            array(
                self::$e2e4Move,
                self::getMove(1, 4, 3, 4),
                self::getMove(7, 5, 4, 2),
                self::getMove(0, 5, 3, 2),
                self::getMove(6, 5, 5, 5),
                self::getMove(0, 6, 2, 5),
                self::getMove(7, 6, 5, 7),
                self::getMove(1, 0, 2, 0),
                self::getMove(7, 4, 7, 6)
            ));
    }

    public function testCastlingKingMoved(): void
    {
        $this->expectException(InvalidMoveException::class);

        $game = self::createClassicGame();
        self::chainOfMoves($game,
            array(
                self::$e2e4Move,
                self::getMove(1, 4, 3, 4),
                self::getMove(7, 5, 4, 2),
                self::getMove(0, 1, 2, 2),
                self::getMove(7, 4, 6, 4),
                self::getMove(1, 0, 2, 0),
                self::getMove(6, 4, 7, 4),
                self::getMove(2, 0, 3, 0),
                self::getMove(7, 6, 5, 5),
                self::getMove(0, 6, 2, 5),
                self::getMove(7, 4, 7, 6)
            ));
    }

    public function testCastlingRookMoved(): void
    {
        $this->expectException(InvalidMoveException::class);

        $game = self::createClassicGame();
        self::chainOfMoves($game,
            array(
                self::$e2e4Move,
                self::getMove(1, 3, 3, 3),
                self::getMove(7, 6, 5, 5),
                self::getMove(0, 2, 4, 6),
                self::getMove(7, 1, 5, 2),
                self::getMove(0, 1, 2, 2),
                self::getMove(5, 2, 3, 3),
                self::getMove(0, 3, 3, 3),
                self::getMove(4, 4, 3, 3),
                self::getMove(0, 0, 0, 2),
                self::getMove(3, 3, 2, 2),
                self::getMove(0, 2, 0, 0),
                self::getMove(7, 5, 6, 4),
                self::getMove(0, 4, 0, 2)
            ));
    }

    public function testCheckButNotKingMoves(): void
    {
        $this->expectException(InvalidMoveException::class);

        $game = self::createClassicGame();
        self::chainOfMoves($game,
            array(
                self::getMove(6, 5, 4, 5),
                self::getMove(1, 4, 3, 4),
                self::getMove(7, 6, 5, 5),
                self::getMove(0, 3, 4, 7),
                self::getMove(7, 1, 5, 2)
            ));
    }
}
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

    private function assertBoardsEquals(array $epxpectedBoard, Board $board): void
    {
        $this->assertEquals(count($epxpectedBoard), $board->getRows());
        for ($row = 0; $row < $board->getRows(); $row++) {
            $this->assertEquals(count($epxpectedBoard[$row]), $board->getCols());
            for ($col = 0; $col < $board->getCols(); $col++) {
                $piece = $board->getPiece(new Position($row, $col)) ?? null;
                $pieceStr = isset($piece) ? strval($piece) : "__";
                $this->assertEquals($epxpectedBoard[$row][$col], $pieceStr);
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

    public function testPlayers(): void
    {
        $game = self::createClassicGame();
        $game->move(self::$e2e4Move);

        $this->assertContains(self::$player1, $game->getPlayers());
        $this->assertContains(self::$player2, $game->getPlayers());
    }

    public function testPawnMove(): void
    {
        $game = self::createClassicGame();
        $game->move(self::$e2e4Move);

        $board = [
            ["BR", "BN", "BB", "BQ", "BK", "BB", "BN", "BR"],
            ["BP", "BP", "BP", "BP", "BP", "BP", "BP", "BP"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["__", "__", "__", "__", "WP", "__", "__", "__"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["WP", "WP", "WP", "WP", "__", "WP", "WP", "WP"],
            ["WR", "WN", "WB", "WQ", "WK", "WB", "WN", "WR"]
        ];
        $this->assertBoardsEquals($board, $game->getBoard(),);
    }

    public function testRookMove(): void
    {
        $game = self::createClassicGame();

        self::chainOfMoves($game,
            array(
                self::getMove(6, 7, 4, 7),
                self::getMove(1, 2, 2, 2),
                self::getMove(7, 7, 5, 7)
            ));
        $board = [
            ["BR", "BN", "BB", "BQ", "BK", "BB", "BN", "BR"],
            ["BP", "BP", "__", "BP", "BP", "BP", "BP", "BP"],
            ["__", "__", "BP", "__", "__", "__", "__", "__"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["__", "__", "__", "__", "__", "__", "__", "WP"],
            ["__", "__", "__", "__", "__", "__", "__", "WR"],
            ["WP", "WP", "WP", "WP", "WP", "WP", "WP", "__"],
            ["WR", "WN", "WB", "WQ", "WK", "WB", "WN", "__"]
        ];

        $this->assertBoardsEquals($board, $game->getBoard(),);
    }

    public function testKnightMove(): void
    {
        $game = self::createClassicGame();

        $game->move(self::getMove(7, 1, 5, 2));
        $board = [
            ["BR", "BN", "BB", "BQ", "BK", "BB", "BN", "BR"],
            ["BP", "BP", "BP", "BP", "BP", "BP", "BP", "BP"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["__", "__", "WN", "__", "__", "__", "__", "__"],
            ["WP", "WP", "WP", "WP", "WP", "WP", "WP", "WP"],
            ["WR", "__", "WB", "WQ", "WK", "WB", "WN", "WR"]
        ];

        $this->assertBoardsEquals($board, $game->getBoard(),);
    }

    public function testBishopMove(): void
    {
        $game = self::createClassicGame();

        self::chainOfMoves($game,
            array(
                self::getMove(7, 1, 5, 2),
                self::getMove(1, 3, 2, 3),
                self::getMove(6, 6, 4, 6),
                self::getMove(0, 2, 4, 6)
            ));
        $board = [
            ["BR", "BN", "__", "BQ", "BK", "BB", "BN", "BR"],
            ["BP", "BP", "BP", "__", "BP", "BP", "BP", "BP"],
            ["__", "__", "__", "BP", "__", "__", "__", "__"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["__", "__", "__", "__", "__", "__", "BB", "__"],
            ["__", "__", "WN", "__", "__", "__", "__", "__"],
            ["WP", "WP", "WP", "WP", "WP", "WP", "__", "WP"],
            ["WR", "__", "WB", "WQ", "WK", "WB", "WN", "WR"]
        ];

        $this->assertBoardsEquals($board, $game->getBoard(),);
    }

    public function testQueenMove(): void
    {
        $game = self::createClassicGame();

        self::chainOfMoves($game,
            array(
                self::$e2e4Move,
                self::getMove(1, 3, 3, 3),
                self::getMove(4, 4, 3, 3),
                self::getMove(0, 3, 3, 3)
            ));

        $board = [
            ["BR", "BN", "BB", "__", "BK", "BB", "BN", "BR"],
            ["BP", "BP", "BP", "__", "BP", "BP", "BP", "BP"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["__", "__", "__", "BQ", "__", "__", "__", "__"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["WP", "WP", "WP", "WP", "__", "WP", "WP", "WP"],
            ["WR", "WN", "WB", "WQ", "WK", "WB", "WN", "WR"]
        ];

        $this->assertBoardsEquals($board, $game->getBoard());
    }

    public function testKingMove(): void
    {
        $game = self::createClassicGame();

        self::chainOfMoves($game,
            array(
                self::getMove(6, 3, 4, 3),
                self::getMove(1, 3, 3, 3),
                self::getMove(7, 4, 6, 3)
            ));

        $board = [
            ["BR", "BN", "BB", "BQ", "BK", "BB", "BN", "BR"],
            ["BP", "BP", "BP", "__", "BP", "BP", "BP", "BP"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["__", "__", "__", "BP", "__", "__", "__", "__"],
            ["__", "__", "__", "WP", "__", "__", "__", "__"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["WP", "WP", "WP", "WK", "WP", "WP", "WP", "WP"],
            ["WR", "WN", "WB", "WQ", "__", "WB", "WN", "WR"]
        ];

        $this->assertBoardsEquals($board, $game->getBoard());
    }

    public function testKidsCheckmate(): void
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

        $board = [
            ["BR", "__", "BB", "BQ", "BK", "BB", "__", "BR"],
            ["BP", "BP", "BP", "BP", "__", "WQ", "BP", "BP"],
            ["__", "__", "BN", "__", "__", "BN", "__", "__"],
            ["__", "__", "__", "__", "BP", "__", "__", "__"],
            ["__", "__", "WB", "__", "WP", "__", "__", "__"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["WP", "WP", "WP", "WP", "__", "WP", "WP", "WP"],
            ["WR", "WN", "WB", "__", "WK", "__", "WN", "WR"]
        ];

        $this->assertBoardsEquals($board, $game->getBoard());
        $this->assertTrue($game->hasColorWon(Color::getWhite()));
    }

    public function testEnPassant(): void
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

        $board = [
            ["BR", "BN", "BB", "BQ", "BK", "BB", "__", "BR"],
            ["BP", "BP", "BP", "__", "BP", "BP", "BP", "BP"],
            ["__", "__", "__", "WP", "__", "BN", "__", "__"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["WP", "WP", "WP", "WP", "__", "WP", "WP", "WP"],
            ["WR", "WN", "WB", "WQ", "WK", "WB", "WN", "WR"]
        ];

        $this->assertBoardsEquals($board, $game->getBoard());
    }

    public function testShortCastling(): void
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


        $board = [
            ["BR", "__", "BB", "BQ", "BK", "BB", "__", "BR"],
            ["BP", "BP", "BP", "BP", "__", "BP", "BP", "BP"],
            ["__", "__", "BN", "__", "__", "BN", "__", "__"],
            ["__", "__", "__", "__", "BP", "__", "__", "__"],
            ["__", "__", "WB", "__", "WP", "__", "__", "__"],
            ["__", "__", "__", "__", "__", "WN", "__", "__"],
            ["WP", "WP", "WP", "WP", "__", "WP", "WP", "WP"],
            ["WR", "WN", "WB", "WQ", "__", "WR", "WK", "__"]
        ];

        $this->assertBoardsEquals($board, $game->getBoard());
    }

    public function testLongCastling()
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

        $board = [
            ["__", "__", "BK", "BR", "__", "BB", "BN", "BR"],
            ["BP", "BP", "BP", "__", "BP", "BP", "BP", "BP"],
            ["__", "__", "BN", "__", "__", "__", "__", "__"],
            ["__", "__", "__", "WP", "__", "__", "__", "__"],
            ["__", "__", "__", "__", "__", "__", "BB", "__"],
            ["__", "__", "__", "__", "__", "WN", "__", "__"],
            ["WP", "WP", "WP", "WP", "__", "WP", "WP", "WP"],
            ["WR", "__", "WB", "WQ", "WK", "WB", "__", "WR"]
        ];

        $this->assertBoardsEquals($board, $game->getBoard());
    }

    public function testPawnTransformation()
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

        $board = [
            ["BR", "BN", "BB", "WR", "__", "__", "__", "BR"],
            ["BP", "BP", "BP", "BP", "BK", "__", "BP", "BP"],
            ["__", "__", "__", "__", "BP", "BN", "__", "__"],
            ["__", "__", "BB", "__", "__", "__", "__", "__"],
            ["__", "__", "__", "__", "BP", "__", "__", "__"],
            ["__", "__", "__", "__", "__", "__", "__", "__"],
            ["WP", "WP", "WP", "WP", "__", "__", "WP", "WP"],
            ["WR", "WN", "WB", "WQ", "WK", "WB", "WN", "WR"]
        ];

        $this->assertBoardsEquals($board, $game->getBoard());
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
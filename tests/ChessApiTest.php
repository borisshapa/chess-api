<?php


namespace tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use utils\Pair;
use const app\HOST;
use const app\PORT;

/**
 * Class ChessApiTest
 * Testing api for chess games.
 * @package tests
 * @see TestCase
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class ChessApiTest extends TestCase
{
    private static Client $client;

    public static function setUpBeforeClass(): void
    {
        $host = HOST;
        $port = PORT;
        self::$client = new Client(["base_uri" => "http://{$host}:{$port}/chess/"]);
    }

    private static function decodeResponse(ResponseInterface $response)
    {
        return json_decode($response->getBody()->getContents());
    }

    private static function responseIsOk($response): bool
    {
        return $response->status == true;
    }

    private static function createGame(string $player1 = null, string $player2 = null): int
    {
        $params = [];
        if (isset($player1)) {
            $params["player1"] = $player1;
        }
        if (isset($player2)) {
            $params["player2"] = $player2;
        }
        $response = self::$client->request("POST", "start", ["form_params" => $params]);
        $data = self::decodeResponse($response);
        self::assertTrue(self::responseIsOk($data));

        return $data->id;
    }

    private static function makeMove(int $id, string $from, string $to, string $pawnPromotion = null)
    {
        $query = [
            "id" => $id,
            "from" => $from,
            "to" => $to
        ];
        if (isset($pawnPromotion)) {
            $query["promotion"] = $pawnPromotion;
        }
        $response = self::$client->request("PUT", "move", ["query" =>
            $query, "http_errors" => false]);
        return self::decodeResponse($response);
    }

    private static function chainOfMoves(int $id, array $moves)
    {
        $data = null;
        foreach ($moves as $move) {
            $data = self::makeMove($id, $move->getFirst(), $move->getSecond());
        }
        return $data;
    }

    private static function getGameStatus(int $id)
    {
        $response = self::$client->request("GET", "status", ["query" => ["id" => $id],
            "http_errors" => false]);
        return self::decodeResponse($response);
    }

    public static function testCreating()
    {
        $game1Id = self::createGame();
        $game2Id = self::createGame();

        self::assertEquals($game1Id + 1, $game2Id);
    }

    public function testStatusGameWithoutNames()
    {
        $gameId = self::createGame();
        $gameStatus = self::getGameStatus($gameId);

        self::assertTrue(self::responseIsOk($gameStatus));
        self::assertEquals("white", $gameStatus->current);
        self::assertEquals("player1", $gameStatus->players->white);
        self::assertEquals("player2", $gameStatus->players->black);
    }

    public static function testStatusWithNames()
    {
        $gameId = self::createGame("Bob", "Alice");
        $gameStatus = self::getGameStatus($gameId);

        self::assertTrue(self::responseIsOk($gameStatus));
        self::assertEquals("white", $gameStatus->current);
        self::assertEquals("Bob", $gameStatus->players->white);
        self::assertEquals("Alice", $gameStatus->players->black);
    }

    public static function testInitialization()
    {
        $gameId = self::createGame();
        $gameStatus = self::getGameStatus($gameId);

        self::assertEquals(BOARD_START_STATE, $gameStatus->board);
    }

    public static function testFinishing()
    {
        $gameId = self::createGame();
        $response = self::$client->request("DELETE", "finish", ["query" =>
            [
                "id" => $gameId
            ]]);
        $data = self::decodeResponse($response);
        self::assertTrue(self::responseIsOk($data));
        self::assertEquals($gameId, $data->id);
        self::assertEquals("Game over. The {$gameId} game has been removed from the database.", $data->message);

        $gameStatus = self::getGameStatus($gameId);
        self::assertFalse(self::responseIsOk($gameStatus));
        self::assertEquals("ChessApiException : DatabaseAccessException : There is not model with {$gameId} id in the database", $gameStatus->message);
    }

    public static function testPawnMove()
    {
        $gameId = self::createGame();

        $moveResponse = self::makeMove($gameId, "e2", "e4");
        self::assertTrue(self::responseIsOk($moveResponse));
        self::assertEquals($gameId, $moveResponse->id);

        $gameStatus = self::getGameStatus($moveResponse->id);
        self::assertTrue(self::responseIsOk($gameStatus));
        self::assertEquals(PAWN_MOVE_BOARD, $gameStatus->board);

        self::assertEquals("black", $gameStatus->current);
    }

    public static function testKidsChessmate()
    {
        $gameId = self::createGame();

        $data = self::chainOfMoves($gameId, array(
            new Pair("e2", "e4"),
            new Pair("e7", "e5"),
            new Pair("d1", "h5"),
            new Pair("b8", "c6"),
            new Pair("f1", "c4"),
            new Pair("g8", "f6"),
            new Pair("h5", "f7")
        ));

        self::assertTrue(self::responseIsOk($data));
        self::assertEquals("White player won. The {$gameId} game has been removed from the database.", $data->message);
    }

    public static function testUnsupportedMove()
    {
        $gameId = self::createGame();
        $data = self::makeMove($gameId, "e3", "e4");
        self::assertFalse(self::responseIsOk($data));
        self::assertEquals("ChessException : InvalidMoveException : There is no piece in (5, 4) position", $data->message);
    }

    public static function testMoveThroughPiece()
    {
        $gameId = self::createGame();
        $data = self::makeMove($gameId, "d1", "h5");
        self::assertFalse(self::responseIsOk($data));
        self::assertEquals("ChessException : InvalidMoveException : Unsupported move: from (7, 3) position, to (3, 7) position", $data->message);
    }

    public static function testShortCastling()
    {
        $gameId = self::createGame();
        $data = self::chainOfMoves($gameId,
            array(
                new Pair("e2", "e4"),
                new Pair("e7", "e5"),
                new Pair("f1", "c4"),
                new Pair("g8", "f6"),
                new Pair("g1", "f3"),
                new Pair("b8", "c6"),
                new Pair("e1", "g1")
            ));
        self::assertTrue(self::responseIsOk($data));

        $status = self::getGameStatus($gameId);
        self::assertEquals(SHORT_CASTLING_BOARD, $status->board);
    }

    public static function testIncorrectCastling()
    {
        $gameId = self::createGame();
        $data = self::chainOfMoves($gameId,
            array(
                new Pair("e2", "e4"),
                new Pair("e7", "e5"),
                new Pair("f1", "c4"),
                new Pair("f8", "c5"),
                new Pair("f2", "f3"),
                new Pair("b8", "c6"),
                new Pair("g1", "h3"),
                new Pair("g8", "f6"),
                new Pair("e1", "g1")
            ));
        self::assertFalse(self::responseIsOk($data));
        self::assertEquals("ChessException : InvalidMoveException : Unable to castle.", $data->message);
    }

    public static function testPawnPromotion()
    {
        $gameId = self::createGame();
        self::chainOfMoves($gameId,
            array(
                new Pair("e2", "e4"),
                new Pair("f7", "f5"),
                new Pair("f2", "f4"),
                new Pair("f5", "e4"),
                new Pair("f4", "f5"),
                new Pair("e7", "e6"),
                new Pair("f5", "f6"),
                new Pair("f8", "c5"),
                new Pair("f6", "f7"),
                new Pair("e8", "e7"),
            ));
        self::makeMove($gameId, "f7", "f8", "rook");
        $data = self::chainOfMoves($gameId,
            array(
                new Pair("g8", "f6"),
                new Pair("f8", "d8")
            ));
        self::assertTrue(self::responseIsOk($data));
        self::assertEquals(PAWN_PROMOTION_BOARD, self::getGameStatus($gameId)->board);
    }

    public function testInvalidRoute()
    {
        $query = [
            "id" => 21,
            "from" => "e2",
            "to" => "e4"
        ];
        $response = self::$client->request("PUT", "mo", ["query" =>
            $query, "http_errors" => false]);
        $data = self::decodeResponse($response);

        self::assertFalse(self::responseIsOk($data));
        self::assertEquals("No route found.", $data->message);
    }

    public static function testNotEnoughOptions(): void
    {
        $gameId = self::createGame();
        $query = [
            "id" => $gameId,
            "from" => "e2",
        ];
        $response = self::$client->request("PUT", "move", ["query" =>
            $query, "http_errors" => false]);
        $data = self::decodeResponse($response);

        self::assertFalse(self::responseIsOk($data));
        self::assertEquals("'id', 'from', 'to' parameters are expected", $data->message);
    }
}
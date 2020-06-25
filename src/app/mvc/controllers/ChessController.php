<?php

namespace app\mvc\controllers;

use api\Controller;
use api\exceptions\DatabaseAccessException;
use api\MySqlModel;
use api\routing\Router;
use app\chess\board\Position;
use app\chess\Color;
use app\chess\exceptions\ChessException;
use app\chess\exceptions\GameException;
use app\chess\game\ClassicGame;
use app\chess\game\Game;
use app\chess\game\initialization\ClassicGameInitialization;
use app\chess\moves\Move;
use app\chess\players\ClassicPlayer;
use app\mvc\models\GameModel;
use const app\PATH_TO_PIECES;

require_once "vendor/autoload.php";

/**
 * Class ChessController
 * Methods processing requests for chess api.
 * Connects api and game engine.
 * When calling methods, the controller accesses
 * the database that stores information about the games.
 * @package app\mvc\controllers
 * @see MySqlModel
 * @see GameModel
 * @see Game
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
class ChessController implements Controller
{
    /**
     * Starts a new chess {@see Game}.
     * Creates a new row in the database
     * with a field 'snapshot' whose value
     * is the row that is the serialization of the created {@see ClassicGame}.
     * @param string $player1 first player name (if not specified, "player1")
     * @param string $player2 second player name (if not specified, "player2")
     * @return false|string a JSON encoded data containing the status (<var>true</var>)
     * and id of the created row on success or <b>FALSE</b> on failure.
     * @throws GameException
     * @see Router::successfulResponse()
     * @see ClassicPlayer
     * @see ClassicGameInitialization
     * @see ClassicGame
     */
    public function start(string $player1 = "player1", string $player2 = "player2")
    {
        $whitePlayer = new ClassicPlayer($player1, Color::getWhite());
        $blackPlayer = new ClassicPlayer($player2, Color::getBlack());

        $game = new ClassicGame($whitePlayer, $blackPlayer);
        $initialization = ClassicGameInitialization::getInstance();
        $game->initialize($initialization);

        $chessModel = new GameModel();
        $id = $chessModel->create(["snapshot" => serialize($game)]);

        return Router::successfulResponse(201, ["id" => $id]);
    }

    /**
     * Returns a JSON with information
     * about the game with the specified id in the database.
     * A successful response is as follows. <br>
     * <code>{
     *  "status" : true,
     *  "players" : {"white":"Bob","black":"Alice"},
     *  "current" : "white",
     *  "board" : [
     *  ["BR","BN","BB","BQ","BK","BB","BN","BR"],
     *  ["BP","BP","BP","BP","BP","BP","BP","BP"],
     *  ["__","__","__","__","__","__","__","__"],
     *  ["__","__","__","__","__","__","__","__"],
     *  ["__","__","__","__","__","__","__","__"],
     *  ["__","__","__","__","__","__","__","__"],
     *  ["WP","WP","WP","WP","WP","WP","WP","WP"],
     *  ["WR","WN","WB","WQ","WK","WB","WN","WR"]]
     * }</code> <br>
     * Returns a bad response if the id parameter was not passed
     * to the api on the corresponding route or the incorrect id was passed.
     * @param int|null $id id of the row in the database table in which information about the necessary game is stored.
     * @return false|string JSON encoded response on success or <b>FALSE</b> on failure.
     * @see Router::badResponse()
     * @see Router::successfulResponse()
     */
    public function status(int $id = null)
    {
        if (!isset($id)) {
            return Router::badResponse(400, "'id' parameter is expected");
        }

        $chessModel = new GameModel();
        try {
            $data = $chessModel->getById($id);
        } catch (DatabaseAccessException $e) {
            return Router::badResponse(404, $e->getMessage());
        }
        $game = unserialize($data->snapshot);

        $players = $game->getPlayers();
        $player1 = $players[0];
        $player2 = $players[1];

        $current = $game->getCurrentPlayer()->getColor()->getName();
        $board = $game->getBoard();

        $jsonBoard = array();
        for ($row = 0; $row < $board->getRows(); $row++) {
            $jsonRow = array();
            for ($col = 0; $col < $board->getCols(); $col++) {
                $piece = $board->getPiece(new Position($row, $col));
                $str = isset($piece) ? strval($piece) : "__";
                array_push($jsonRow, $str);
            }
            array_push($jsonBoard, $jsonRow);
        }

        $data = [
            "players" => array(
                "white" => $player1->getName(),
                "black" => $player2->getName()),
            "current" => strtolower($current),
            "board" => $jsonBoard
        ];
        return Router::successfulResponse(200, $data);
    }

    /**
     * Makes a chess move.
     * Finds a row in the database table with the specified id,
     * makes a move and overwrites information about the current state of the game.
     * A player makes a move defined by a field {@see AbstractGame::$currentPlayer},
     * which is stored in the database in serialized form. <br>
     *
     * If after the move, the player wins, then the corresponding row in the database table is deleted,
     * and the JSON with victory information is returned. <br>
     *
     * In the case of a successful move, a JSON containing the status of action (<var>true</var>), the
     * id of the row of the database table where the changes were made and the message about the success move or
     * victory of one of the players is returned. <br>
     *
     * In the case that one of the following parameters is not passed in the api: <var>id</var>, <var>from</var>, <var>to</var>
     * the row with the specified id was not found or the move is contrary to the rules, a bad response is returned. <br>
     *
     * Bad response contains status (<var>false</var>) and error message.
     * @param int|null $id id of the game in the database in which you want to make a move.
     * @param string|null $from the position of the piece that is walking. In chess notation ({@example "e2"}).
     * @param string|null $to the position in which the piece is after the move. In chess notation ({@example "e4"}).
     * @param string|null $piece if a pawn moves to a far row from its initial position,
     * then it turns into this piece. If another move was made, then this parameter is ignored,
     * also if the parameter was not passed with such a move, the pawn remains itself.
     * The name of the piece is case insensitive. ({@example "rook", "RoOk"})
     * @return false|string JSON encoded response on success or <b>FALSE</b> on failure.
     * @see Router::successfulResponse()
     * @see Router::badResponse()
     * @see Game::move()
     */
    public function move(int $id = null, string $from = null, string $to = null, string $piece = null)
    {
        if (!isset($id) || !isset($from) || !isset($to)) {
            return Router::badResponse(400, "'id', 'from', 'to' parameters are expected");
        }

        $chessModel = new GameModel();
        try {
            $data = $chessModel->getById($id);
        } catch (DatabaseAccessException $e) {
            return Router::badResponse(404, $e->getMessage());
        }
        $game = unserialize($data->snapshot);
        $color = $game->getCurrentPlayer()->getColor();

        $rows = $game->getBoard()->getRows();
        if (isset($piece)) {
            $piece = ucfirst(strtolower($piece));
            $piece = PATH_TO_PIECES . $piece;
        }

        try {
            $game->move(new Move($this->parsePosition($from, $rows), $this->parsePosition($to, $rows)), $piece);
        } catch (ChessException $e) {
            return Router::badResponse(400, $e->getMessage());
        }

        if ($game->checkIfColorWon($color)) {
            $chessModel->delete($id);
            return Router::successfulResponse(200,
                [
                    "id" => $id,
                    "message" => "{$color->getName()} player won. The {$id} game has been removed from the database."
                ]
            );
        } else {
            $chessModel->updateById($id, ["snapshot" => serialize($game)]);
            return Router::successfulResponse(200,
                [
                    "id" => $id,
                    "message" => "A move {$from}-{$to} has been made."
                ]
            );
        }
    }

    /**
     * Finishes the chess game.
     * Deletes the row with the specified id from the database table.
     * In the case of a successful deletion,
     * JSON is returned with the status(<var>true</var>), id, deleted row and a message that the game is over.
     * If the row with the specified id is not in the database table,
     * then a bad response is returned with the status(<var>false</var>) and error message.
     * @param int|null $id what to delete
     * @return false|string JSON encoded response on success or <b>FALSE</b> on failure.
     * @see Model::delete()
     * @see Router::badResponse()
     * @see Router::successfulResponse()
     */
    public function finish(int $id = null)
    {
        if (!isset($id)) {
            return Router::badResponse(400, "'id' parameter is expected");
        }

        $chessModel = new GameModel();
        $chessModel->delete($id);
        return Router::successfulResponse(200,
            [
                "id" => $id,
                "message" => "Game over. The {$id} game has been removed from the database."
            ]
        );
    }

    private function parsePosition(string $position, int $rows)
    {
        $position = strtolower($position);
        $row = $rows - $position[1];
        $col = ord($position[0]) - ord('a');
        return new Position($row, $col);
    }
}
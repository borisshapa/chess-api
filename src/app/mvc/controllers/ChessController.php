<?php

namespace app\mvc\controllers;

use api\Controller;
use api\exceptions\DatabaseAccessException;
use api\routing\Router;
use app\chess\board\Position;
use app\chess\Color;
use app\chess\exceptions\ChessException;
use app\chess\game\ClassicGame;
use app\chess\game\initialization\ClassicGameInitialization;
use app\chess\moves\Move;
use app\chess\players\ClassicPlayer;
use app\mvc\models\ChessModel;
use PDO;
use const app\PATH_TO_PIECES;

require_once "vendor/autoload.php";

class ChessController implements Controller
{
    public function start(string $player1 = "player1", string $player2 = "player2")
    {
        $whitePlayer = new ClassicPlayer($player1, Color::getWhite());
        $blackPlayer = new ClassicPlayer($player2, Color::getBlack());

        $game = new ClassicGame($whitePlayer, $blackPlayer);
        $initialization = ClassicGameInitialization::getInstance();
        $game->initialize($initialization);

        $chessModel = new ChessModel();
        $id = $chessModel->create(["snapshot" => serialize($game)]);

        return Router::successfulResponse(201, ["id" => $id]);
    }

    public function status(int $id = null)
    {
        if (!isset($id)) {
            return Router::badResponse(400, "'id' parameter is expected");
        }

        $chessModel = new ChessModel();
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
            "status" => "200",
            "players" => array(
                "white" => $player1->getName(),
                "black" => $player2->getName()),
            "current" => strtolower($current),
            "board" => $jsonBoard
        ];
        return json_encode($data);
    }

    public function move(int $id = null, string $from = null, string $to = null, string $piece = null)
    {
        if (!isset($id) || !isset($from) || !isset($to)) {
            return Router::badResponse(400, "'id', 'from', 'to' parameters are expected");
        }

        $chessModel = new ChessModel();
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

    public function finish(int $id = null)
    {
        if (!isset($id)) {
            return Router::badResponse(400, "'id' parameter is expected");
        }

        $chessModel = new ChessModel();
        $chessModel->delete($id);
        return Router::successfulResponse(200,
            [
                "id" => $id,
                "message" => "Game over. The {$id} game has been removed from the database."
            ]
        );
    }

    public function parsePosition(string $position, int $rows)
    {
        $position = strtolower($position);
        $row = $rows - $position[1];
        $col = ord($position[0]) - ord('a');
        return new Position($row, $col);
    }
}
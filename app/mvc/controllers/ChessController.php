<?php

namespace app\mvc\controllers;

use api\Controller;
use app\chess\board\Position;
use app\chess\Color;
use app\chess\game\ClassicGame;
use app\chess\game\initialization\ClassicGameInitialization;
use app\chess\moves\Move;
use app\chess\players\ClassicPlayer;
use app\mvc\models\ChessModel;
use PDO;

include "app/chess/pieces/Pieces.php";
require_once "vendor/autoload.php";

class ChessController extends Controller
{
    public function start(string $player1 = "player1", string $player2 = "player2")
    {
        $whitePlayer = new ClassicPlayer($player1, Color::getWhite());
        $blackPlayer = new ClassicPlayer($player2, Color::getBlack());

        $game = new ClassicGame(array($whitePlayer, $blackPlayer));
        $initialization = new ClassicGameInitialization();

        $game->initialize($initialization);
        $chessModel = new ChessModel();
        $id = $chessModel->create(["snapshot" => serialize($game)]);

        $response = array("status" => 200, "id" => $id);
        return json_encode($response);
    }

    public function status(int $id)
    {
        $chessModel = new ChessModel();
        $data = $chessModel->getById($id);
        $game = unserialize($data->snapshot);

        $players = $game->getPlayers();
        $player1 = $players[0];
        $player2 = $players[1];

        $current = $players[$game->getCurrentPlayer()]->getColor()->getName();
        $board = $game->getBoard();

        $jsonBoard = array();
        for ($row = 0; $row < $board->getRows(); $row++) {
            $jsonRow = array();
            for ($col = 0; $col < $board->getCols(); $col++) {
                $piece = $board->getPiece(new Position($row, $col));
                $str = isset($piece) ? strval($piece) : "0";
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

    public function move(int $id, string $from, string $to)
    {
        $chessModel = new ChessModel();
        $data = $chessModel->getById($id);
        $game = unserialize($data->snapshot);

        $players = $game->getPlayers();
        $color = $players[$game->getCurrentPlayer()]->getColor();

        $rows = $game->getBoard()->getRows();
        $game->move(new Move($this->parsePosition($from, $rows), $this->parsePosition($to, $rows)));

        if ($game->winningConditions($color)) {
            var_dump("I AM GENIOUS");
            $chessModel->delete($id);
        } else {
            $chessModel->updateById($id, ["snapshot" => serialize($game)]);
        }
    }

    public function parsePosition(string $position, int $rows) {
        $position = strtolower($position);
        $row = $rows - $position[1];
        $col = ord($position[0]) - ord('a');
        return new Position($row, $col);
    }
}
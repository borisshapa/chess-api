<?php

namespace app\mvc\controllers;

use api\Controller;
use app\chess\Color;
use app\chess\game\ClassicGame;
use app\chess\game\initialization\ClassicGameInitialization;
use app\chess\players\ClassicPlayer;
use app\mvc\models\ChessModel;
use PDO;


class ChessController extends Controller
{
    private static $board;

    public function start(string $player1 = "player1", string $player2 = "player2") {
        $whitePlayer = new ClassicPlayer($player1, Color::getWhite());
        $blackPlayer = new ClassicPlayer($player2, Color::getBlack());

        $game = new ClassicGame(array($whitePlayer, $blackPlayer));
        $initialization = new ClassicGameInitialization();

        $game->initialize($initialization);
        $data = [
            "players" => serialize($game->getPlayers()),
            "current" => serialize($game->getCurrentPlayer()),
            "board" => serialize($game->getBoard())
        ];
        var_dump($data);
        $chessModel = new ChessModel();
        $chessModel->create($data);
    }

    public function getBoard() {
        var_dump(self::$board);
    }
}
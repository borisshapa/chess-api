<?php

namespace chess\game;

use chess\board\ClassicBoard;
use chess\game\init\GameInitialization;
use chess\pieces\Black;
use chess\pieces\White;
use chess\players\ClassicalPlayer;
use chess\players\Player;

/*
 * TODO
 *  to abstract
 */
class ClassicGame extends AbstractGame
{

    public function initialize(GameInitialization $initialization)
    {
        /*
         * TODO
         *  Players
         *  Board
         */

        $player1 = new ClassicalPlayer("Bob", new White());
        $player2 = new ClassicalPlayer("Alice", new Black());
        $initialization->initialize(array($player1, $player2), $this->getBoard());
    }

    public function start()
    {

    }

    private function getFirstPlayer(): Player
    {
        $players = $this->getPlayers();
        /*
         * TODO
         *  check if there is white player
         */
        return array_filter($players, function (Player $player) {
            return $player->getColor() == "white";
        })[0];
    }

    public function winningConditions()
    {
        // TODO: Implement winningConditions() method.
    }
}
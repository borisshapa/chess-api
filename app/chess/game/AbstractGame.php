<?php

namespace app\chess\game;

use app\chess\board\Board;
use app\chess\board\Position;
use app\chess\Color;
use app\chess\exceptions\GameException;
use app\chess\game\initialization\GameInitialization;
use app\chess\moves\Move;
use app\chess\players\Player;

require_once "vendor/autoload.php";

abstract class AbstractGame implements Game
{
    private $players;
    protected $board;
    private $currentPlayer;

    protected function __construct(array $players, Board $board, Color $currentColor)
    {
        $this->players = $players;
        $this->board = $board;
        $this->currentPlayer = $this->findPlayerWithColor($currentColor);
    }

    public function initialize(GameInitialization $initialization)
    {
        $initialization->initialize($this->players, $this->getBoard());
    }


    protected function nextPlayer()
    {
        $this->currentPlayer++;
        $this->currentPlayer %= count($this->players);
    }

    protected function findPlayerWithColor(Color $color) {
        $ind = key(array_filter($this->players, function (Player $player) use($color) {
            return $player->getColor() === $color;
        }));
        if (!isset($ind)) {
            throw new \Exception("At least one player with a starting color is expected");
        }
        return $ind;
    }

    /**
     * @return mixed
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * @return Board
     */
    public function getBoard(): Board
    {
        return $this->board;
    }

    /**
     * @return mixed
     */
    public function getCurrentPlayer()
    {
        return $this->currentPlayer;
    }
}
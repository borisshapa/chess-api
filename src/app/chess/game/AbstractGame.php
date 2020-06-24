<?php

namespace app\chess\game;

use app\chess\board\Board;
use app\chess\Color;
use app\chess\exceptions\GameException;
use app\chess\game\initialization\GameInitialization;
use app\chess\players\Player;

abstract class AbstractGame implements Game
{
    protected array $players;
    protected Board $board;
    protected int $currentPlayer;

    protected function __construct(array $players, Board $board, Color $currentColor)
    {
        $this->players = $players;
        $this->board = $board;
        $this->currentPlayer = $this->findPlayerWithColor($currentColor);
    }

    public function initialize(GameInitialization $initialization): void
    {
        $initialization->initialize($this->players, $this->getBoard());
    }


    protected function nextPlayer(): void
    {
        $this->currentPlayer++;
        $this->currentPlayer %= count($this->players);
    }

    protected function findPlayerWithColor(Color $color): int
    {
        $ind = array_key_first(array_filter($this->players,
            function (Player $player) use ($color) {
                return $player->getColor() === $color;
            }));
        if (!isset($ind)) {
            throw new GameException("At least one player with a starting color is expected");
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
    public function getCurrentPlayer(): Player
    {
        return $this->players[$this->currentPlayer];
    }
}
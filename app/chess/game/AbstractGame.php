<?php

namespace app\chess\game;

use app\chess\board\Board;
use app\chess\board\Position;
use app\chess\Color;
use app\chess\exceptions\GameException;
use app\chess\game\initialization\GameInitialization;
use app\chess\players\Player;

abstract class AbstractGame implements Game
{
    private $players;
    private $board;
    private $startingColor;
    private $currentPlayer;

    protected function __construct(array $players, Board $board, Color $startingColor)
    {
        $this->players = $players;
        $this->board = $board;
        $this->startingColor = $startingColor;
    }

    public function initialize(GameInitialization $initialization)
    {
        $ind = key(array_filter($this->players, function (Player $player) {
            return $player->getColor()->getName() === "White";
        }));
        if (!isset($ind)) {
            throw new \Exception("At least one player with a starting color is expected");
        }
        $this->currentPlayer = $ind;
        var_dump($this->getBoard()->getRows());
        $initialization->initialize($this->players, $this->getBoard());
    }

    public function start()
    {
        echo "ROFL";
//        while (true) {
//            $player = $this->nextPlayer();
//            // TODO: Think about exceptions
//        }
    }

    private function nextPlayer() : Player
    {
        $this->currentPlayer++;
        $this->currentPlayer %= count($this->players);
        return $this->players[$this->currentPlayer];
    }

    private function checkIfPlayerHasPiece(Player $player, Position $where) {
        $piece = $this->getBoard()->getPiece($where);
        if ($piece == null) {
            throw new GameException(sprintf("Position (%d, %d) is empty", $where->getCol(), $where->getRow()));
        }

        if ($player->getColor() !== $piece->getColor()) {
            throw new GameException(sprintf("Piece at position (%d, %d) is not yours", $where->getCol(), $where->getRow()));
        }
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
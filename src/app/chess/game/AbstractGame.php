<?php

namespace app\chess\game;

use app\chess\board\Board;
use app\chess\Color;
use app\chess\exceptions\GameException;
use app\chess\game\initialization\GameInitialization;
use app\chess\players\Player;

/**
 * Class AbstractGame
 * Template for creating a game of chess.
 * @package app\chess\game
 * @see Game
 * @author Boris Shaposhnikov bshaposhnikov01@gmail.com
 */
abstract class AbstractGame implements Game
{
    /**
     * @var array chess players
     */
    protected array $players;
    /**
     * @var Board game board
     */
    protected Board $board;
    /**
     * @var int the number of the player in the array {@see AbstractGame::$players} that makes the move.
     */
    protected int $currentPlayer;

    /**
     * AbstractGame constructor.
     * Creates a game
     * @param array $players players list
     * @param Board $board game board
     * @param Color $currentColor the color of the player to walk.
     * (If the game is being created for the first time, then this is the color of the player who moves first)
     * @throws GameException if in the passed list of players
     * there is no player with the color from which the move is expected.
     */
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

    /**
     * Changes {@see AbstractGame::$currentPlayer} to next in the {@see AbstractGame::$players} array.
     * (0 follows the last)
     */
    protected function nextPlayer(): void
    {
        $this->currentPlayer++;
        $this->currentPlayer %= count($this->players);
    }

    /**
     * Returns the index of the player with the specified color in the {@see AbstractGame::$players} array.
     * @param Color $color desired color
     * @return int the index of the player with the specified color in the {@see AbstractGame::$players} array.
     * @throws GameException if the player with specified color not found
     */
    protected function findPlayerWithColor(Color $color): int
    {
        $ind = array_key_first(array_filter($this->players,
            function (Player $player) use ($color) {
                return $player->getColor() == $color;
            }));
        if (!isset($ind)) {
            throw new GameException("At least one player with a starting color is expected");
        }
        return $ind;
    }

    /**
     * @return array player list
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * @return Board the current state of the board.
     */
    public function getBoard(): Board
    {
        return $this->board;
    }

    /**
     * @return Player player who makes the move
     */
    public function getCurrentPlayer(): Player
    {
        return $this->players[$this->currentPlayer];
    }
}
<?php

namespace chess\game;

use chess\board\Board;

abstract class AbstractGame implements Game
{
    private array $players;
    private Board $board;

    protected function __construct(array $players, Board $board)
    {
        $this->players = $players;
        $this->board = $board;
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
}
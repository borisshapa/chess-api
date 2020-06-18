<?php

namespace chess\game\init;

use chess\board\Board;

interface GameInitialization
{
    public function initialize(array $players, Board $board);
}
<?php

namespace app\chess\game\initialization;

use app\chess\board\Board;

interface GameInitialization
{
    public function initialize(array $players, Board $board);
}
<?php

namespace chess\game;

use chess\game\init\GameInitialization;

interface Game
{
    public function initialize(GameInitialization $initialization);

    public function start();

    public function winningConditions();
}
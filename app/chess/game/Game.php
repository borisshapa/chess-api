<?php

namespace app\chess\game;


use app\chess\game\initialization\GameInitialization;

interface Game
{
    public function initialize(GameInitialization $initialization);

    public function start();

    public function winningConditions();
}
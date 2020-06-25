<?php

namespace app;

use api\routing\Route;
use api\routing\Router;

/**
 * Start a new chess game
 */
Router::addRoute(new Route("/start/", Route::METHOD_POST, "ChessController@start"));
/**
 * Find out the status of a chess game.
 */
Router::addRoute(new Route("/status/", Route::METHOD_GET, "ChessController@status"));
/**
 * Make a move
 */
Router::addRoute(new Route("/move/", Route::METHOD_PUT, "ChessController@move"));
/**
 * Finish a chess game
 */
Router::addRoute(new Route("/finish/", Route::METHOD_DELETE, "ChessController@finish"));
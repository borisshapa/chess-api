<?php

namespace app;

use api\routing\Route;
use api\routing\Router;

Router::addRoute(new Route('/start/',Route::METHOD_GET, 'ChessController@start'));
Router::addRoute(new Route('/status/',Route::METHOD_GET, 'ChessController@status'));
Router::addRoute(new Route('/move/', Route::METHOD_GET, 'ChessController@move'));
<?php

namespace app;

/**
 * The name of the database in which the chess game information is stored.
 */
const DB_NAME = "chess";
/**
 * At what host to contact api.
 */
const HOST = "localhost";
/**
 * At what port to contact api.
 */
const PORT = "8080";
/**
 * Username for accessing the database.
 */
const USERNAME = "root";
/**
 * The namespace of the controller classes that process requests to api.
 */
const PATH_TO_CONTROLLERS = "app\mvc\controllers\\";
/**
 * The namespace of the classes in charge of chess pieces.
 */
const PATH_TO_PIECES = "app\chess\pieces\\";
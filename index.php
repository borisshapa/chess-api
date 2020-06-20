<?php

namespace endpoint;

use api\routing\Request;
use api\routing\Router;

require_once "vendor/autoload.php";

echo (new Router(new Request()))->getContent();
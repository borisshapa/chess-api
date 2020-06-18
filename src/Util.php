<?php

namespace chess\util;

use Throwable;
use chess\exceptions\base\NullPointerException;

class Objects {
    public static function requireNonNull($obj, string $errorMessage) {
        if ($obj == null) {
            throw new NullPointerException("Expected non-null object");
        }
    }
}

function error(Throwable $exception) {

}
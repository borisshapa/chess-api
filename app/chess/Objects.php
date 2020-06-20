<?php


namespace app\chess;


use app\chess\exceptions\NullPointerException;

class Objects {
    public static function requireNonNull($obj, string $errorMessage) {
        if ($obj == null) {
            throw new NullPointerException("Expected non-null object");
        }
    }
}
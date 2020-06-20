<?php

namespace app\chess;

use app\chess\exceptions\NullPointerException;
use Throwable;

require_once "vendor/autoload.php";

class Util
{
    public static function getSerializedContent(string $serialized)
    {
        if ($serialized[0] === 'C') {
            $ind = strpos($serialized, "{");
            return substr($serialized, $ind, strlen($serialized) - $ind);
        } else if (substr($serialized, 0, 6) === "string") {
            $ind = strpos($serialized, "\"");
            return substr($serialized, $ind, strlen($serialized) - $ind);
        }
    }
}
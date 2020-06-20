<?php

$arr = array(3, 1, 5, 7);

$ind = key(array_filter($arr, function (int $x) {
    return $x % 2 == 0;
}));

echo $ind == null;
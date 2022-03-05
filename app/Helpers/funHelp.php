<?php

function searchArray($array, $field, $q)
{
    $result = [];
    foreach ($array as $key => $val) {
        $leftstr = strtolower($val[$field]);
        $rightstr = strtolower($q);
        if (strpos($leftstr, $rightstr) !== false) {
            $result[] = $val;
        }
    }
    return $result;
} 

?>
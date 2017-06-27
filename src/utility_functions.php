<?php
function starts_with($haystack, $needle) {
    return strpos($haystack, $needle) === 0;
}

function trim_leading_string($haystack, $string) {
    $string = preg_quote($string);
    $result = preg_replace("/^$string/", '', $haystack);
    return $result;
}
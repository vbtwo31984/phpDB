<?php
/**
 * @param string $haystack
 * @param string $needle
 * @return bool
 * Checks if $haystack starts with $needle
 */
function starts_with($haystack, $needle) {
    return strpos($haystack, $needle) === 0;
}

/**
 * @param string $haystack
 * @param string $string
 * @return string
 * Removes the $string from the beginning of $haystack
 */
function trim_leading_string($haystack, $string) {
    $string = preg_quote($string);
    $result = preg_replace("/^$string/", '', $haystack);
    return $result;
}
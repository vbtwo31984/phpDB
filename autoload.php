<?php
require_once __DIR__.'/src/utility_functions.php';

// in production, would use Composer to manage dependencies and autoloading, since no 3rd party helpers are allowed,
// register my own autoload function
spl_autoload_register(function($class) {
    // only do autoloading for my classes (namespace PhpDB) - prevents from trying to autoload PHPUnit classes when
    // running tests
    if(strpos($class, 'PhpDB') === 0) {
        // remove PhpDB\ prefix
        $class = preg_replace('/^PhpDB\\\\/', '', $class);
        // invert backslashes to forward slashes for including
        $class = str_replace('\\', '/', $class);

        require_once __DIR__ . '/src/' . $class . '.php';
    }
});
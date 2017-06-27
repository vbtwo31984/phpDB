<?php
require_once __DIR__.'/autoload.php';

$db = new \PhpDB\PhpDB(STDIN, STDOUT);
$db->run();
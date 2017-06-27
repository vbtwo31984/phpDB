<?php

use PhpDB\Database;
use PHPUnit\Framework\TestCase;

class DataBaseTest extends TestCase
{
    public function testCanCreateDatabaseWithName()
    {
        $database = new Database('test');
        $this->assertEquals('test', $database->getName());

        $database2 = new Database('test2');
        $this->assertEquals('test2', $database2->getName());
    }
}

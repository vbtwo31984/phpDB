<?php

use PhpDB\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testCanCreateDatabaseWithName()
    {
        $database = new Database('test');
        $this->assertEquals('test', $database->getName());

        $database2 = new Database('test2');
        $this->assertEquals('test2', $database2->getName());
    }

    /**
     * @expectedException PhpDB\Exceptions\InvalidNameException
     * @expectedExceptionMessage Name test one is invalid
     */
    public function testNameWithSpacesThrowsException()
    {
        new Database('test one');
    }

    /**
     * @expectedException PhpDB\Exceptions\InvalidNameException
     * @expectedExceptionMessage Name test,one is invalid
     */
    public function testNameWithSpecialCharactersThrowsException()
    {
        new Database('test,one');
    }
}

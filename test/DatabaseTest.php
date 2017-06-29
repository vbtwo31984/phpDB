<?php

use PhpDB\Database;
use PhpDB\Table;
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

    public function testGettingNonExistantTableReturnsNull() {
        $database = new Database('test');
        $table = $database->getTable('abc');
        $this->assertNull($table);
    }

    public function testCanAddAndRetrieveTable()
    {
        $database = new Database('test');
        $table = new Table('table1', ['id'=>'int']);
        $database->addTable($table);
        $retrievedTable = $database->getTable('table1');
        $this->assertEquals('table1', $retrievedTable->getName());
    }

    public function testCanGetTableNames()
    {
        $database = new Database('test');
        $table = new Table('table1', ['id'=>'int']);
        $database->addTable($table);
        $table = new Table('table2', ['id'=>'int']);
        $database->addTable($table);

        $tableNames = $database->getListOfTables();
        $this->assertCount(2, $tableNames);
        $this->assertContains('table1', $tableNames);
        $this->assertContains('table2', $tableNames);
    }

    /**
     * @expectedException \PhpDB\Exceptions\DuplicateTableException
     * @expectedExceptionMessage Table table1 already exists
     */
    public function testAddingDuplicateTableThrowsError()
    {
        $database = new Database('test');
        $table = new Table('table1', ['id'=>'int']);
        $database->addTable($table);
        $database->addTable($table);
    }
}

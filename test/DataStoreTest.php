<?php

use PhpDB\Database;
use PhpDB\DataStore;
use PHPUnit\Framework\TestCase;

class DataStoreTest extends TestCase
{
    /** @var DataStore */
    private $dataStore;

    protected function setUp()
    {
        parent::setUp();

        $this->dataStore = new DataStore();
    }

    public function testCanGetEmptyListOfDatabases()
    {
        $listOfDbs = $this->dataStore->getListOfDatabases();
        $this->assertEmpty($listOfDbs);
    }

    public function testCanAddDatabase()
    {
        $database = new Database('test');
        $this->dataStore->addDatabase($database);
        $listOfDbs = $this->dataStore->getListOfDatabases();
        $this->assertCount(1, $listOfDbs);
        $this->assertContains('test', $listOfDbs);
    }

    public function testCanAddTwoDatabases()
    {
        $database = new Database('test');
        $database2 = new Database('test2');
        $this->dataStore->addDatabase($database);
        $this->dataStore->addDatabase($database2);
        $listOfDbs = $this->dataStore->getListOfDatabases();
        $this->assertCount(2, $listOfDbs);
        $this->assertContains('test', $listOfDbs);
        $this->assertContains('test2', $listOfDbs);
    }

    /**
     * @expectedException PhpDB\Exceptions\DuplicateDatabaseException
     * @expectedExceptionMessage Database test already exists
     */
    public function testCannotAddDatabasesWithDuplicateName()
    {
        $database = new Database('test');
        $this->dataStore->addDatabase($database);
        $this->dataStore->addDatabase($database);
    }

    public function testCanGetDatabaseByName()
    {
        $database = new Database('test');
        $this->dataStore->addDatabase($database);

        $returnedDatabase = $this->dataStore->getDatabase('test');
        $this->assertEquals('test', $returnedDatabase->getName());
    }
}

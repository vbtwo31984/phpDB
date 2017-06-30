<?php

use PhpDB\PhpDB;
use PHPUnit\Framework\TestCase;

class PhpDBTest extends TestCase
{
    private $inputStream;
    private $outputStream;
    /** @var PhpDB */
    private $db;

    protected function setUp()
    {
        parent::setUp();

        // The input stream used to send commands to the app
        $this->inputStream = fopen('php://memory', 'w');
        // The output stream is used to read output from the app
        $this->outputStream = fopen('php://memory', 'w');

        $this->db = new PhpDB($this->inputStream, $this->outputStream);
    }

    public function testCanRunAndQuitDB()
    {
        fwrite($this->inputStream, "quit\n");
        rewind($this->inputStream);
        $this->db->run();
        rewind($this->outputStream);
        $line = stream_get_line($this->outputStream, 1024);
        $this->assertEquals("Welcome to PhpDB. Enter a command or 'quit' to exit\n> Bye\n", $line);
    }

    public function testCanRunAndExecuteCommands()
    {
        fwrite($this->inputStream, "list databases\nquit\n");
        rewind($this->inputStream);
        $this->db->run();
        rewind($this->outputStream);
        $line = stream_get_line($this->outputStream, 1024);
        $this->assertEquals("Welcome to PhpDB. Enter a command or 'quit' to exit\n> No databases\n> Bye\n", $line);
    }

    public function testUnknownCommandReturnsError()
    {
        $result = $this->db->processCommand('Testing');
        $this->assertEquals('Unknown command', $result);
    }

    public function testListDatabasesReturnsMessageWhenNoDatabasesExist()
    {
        $result = $this->db->processCommand('list databases');
        $this->assertEquals('No databases', $result);
    }

    public function testListDatabasesReturnsDatabaseNameAfterAddingDatabase()
    {
        $result = $this->db->processCommand('create database test');
        $this->assertEquals('Database test created', $result);
        $result = $this->db->processCommand('list databases');
        $this->assertEquals('test', $result);
    }

    public function testListDatabasesReturnsDatabaseNamesAfterAddingMultipleDatabases()
    {
        $result = $this->db->processCommand('create database test');
        $this->assertEquals('Database test created', $result);
        $result = $this->db->processCommand('create database test2');
        $this->assertEquals('Database test2 created', $result);
        $result = $this->db->processCommand('list databases');
        $this->assertEquals('test, test2', $result);
    }

    public function testCreatingDuplicateDatabasesReturnsError()
    {
        $this->db->processCommand('create database test');
        $result = $this->db->processCommand('create database test');

        $this->assertEquals('Database test already exists', $result);
    }

    public function testCreatingDatabaseWithInvalidNameReturnsError()
    {
        $result = $this->db->processCommand('create database test one');
        $this->assertEquals('Name test one is invalid', $result);
    }

    public function testUseDatabaseCommandSwitchesDatabases()
    {
        $this->db->processCommand('create database test');
        $result = $this->db->processCommand('use database test');
        $this->assertEquals('Active database: test', $result);
    }

    public function testUseNonExistantDatabaseReturnsError()
    {
        $result = $this->db->processCommand('use database test');
        $this->assertEquals('Unknown database test', $result);
    }

    public function testListTablesReturnsErrorMessageWithoutUsingDatabase()
    {
        $result = $this->db->processCommand('list tables');

        $this->assertEquals('No active database', $result);
    }

    public function testListTablesReturnsMessageWhenNoTablesExist()
    {
        $this->db->processCommand('create database test');
        $this->db->processCommand('use database test');
        $result = $this->db->processCommand('list tables');

        $this->assertEquals('No tables in database test', $result);
    }

    public function testCreateTableWithoutUsingDatabaseReturnsError()
    {
        $result = $this->db->processCommand('create table test1 (id int, name varchar)');
        $this->assertEquals('No active database', $result);
    }

    public function testListTablesAfterCreateTableReturnsTableName()
    {
        $this->db->processCommand('create database test');
        $this->db->processCommand('use database test');
        $result = $this->db->processCommand('create table table1 (id int)');
        $this->assertEquals('Table table1 created', $result);
        $result = $this->db->processCommand('list tables');
        $this->assertEquals('table1', $result);
    }

    public function testSelectFromEmptyTableReturnsNoRowsMessage()
    {
        $this->db->processCommand('create database test');
        $this->db->processCommand('use database test');
        $this->db->processCommand('create table table1 (id int, name varchar)');
        $result = $this->db->processCommand('select * from table1');
        $this->assertEquals('No rows', $result);
    }

    public function testSelectWhenNoActiveDatabaseReturnsMessage()
    {
        $result = $this->db->processCommand('select * from table1');
        $this->assertEquals('No active database', $result);
    }

    public function testSelectFromNonExistingTableReturnsMessage()
    {
        $this->db->processCommand('create database test');
        $this->db->processCommand('use database test');
        $result = $this->db->processCommand('select * from table1');
        $this->assertEquals('Table table1 does not exist', $result);
    }

//    public function testCanInsertRowAndSelectIt()
//    {
//
//        $this->db->processCommand('create database test');
//        $this->db->processCommand('use database test');
//        $this->db->processCommand('create table table1 (id int, name varchar)');
//        $this->db->processCommand("insert into table1 (id, name) values (1, 'John')");
//        $result = $this->db->processCommand('select * from table1');
//        $this->assertEquals("id, name\n1, 'John'", $result);
//    }
}

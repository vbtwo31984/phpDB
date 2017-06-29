<?php
use PhpDB\Table;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    /**
     * @expectedException PhpDB\Exceptions\InvalidTableDefinitionException
     * @expectedExceptionMessage No columns defined
     */
    public function testCreatingTableWithNoColumnsThrowsError()
    {
        new Table('test', null);
    }

    /**
     * @expectedException PhpDB\Exceptions\InvalidNameException
     * @expectedExceptionMessage Name test one is invalid
     */
    public function testCreatingTableWithInvalidNameThrowsError()
    {
        new Table('test one', ['id'=>'int']);
    }

    public function testCanCreateTableWithNameAndIntColumn()
    {
        $table = new Table('test', ['id'=>'int']);
        $this->assertEquals('test', $table->getName());
        $this->assertCount(1, $table->getColumnDefinitions());
        $this->assertArrayHasKey('id', $table->getColumnDefinitions());
        $this->assertEquals('int', $table->getColumnDefinitions()['id']);
    }

    public function testCanCreateTableWithNameAndVarcharColumn()
    {
        $table = new Table('test', ['name'=>'varchar']);
        $this->assertEquals('test', $table->getName());
        $this->assertCount(1, $table->getColumnDefinitions());
        $this->assertArrayHasKey('name', $table->getColumnDefinitions());
        $this->assertEquals('varchar', $table->getColumnDefinitions()['name']);
    }

    public function testCanCreateTableWithMultipleColumns()
    {
        $table = new Table('test', ['id'=>'int', 'name'=>'varchar']);
        $this->assertEquals('test', $table->getName());
        $this->assertCount(2, $table->getColumnDefinitions());
        $this->assertArrayHasKey('id', $table->getColumnDefinitions());
        $this->assertEquals('int', $table->getColumnDefinitions()['id']);
        $this->assertArrayHasKey('name', $table->getColumnDefinitions());
        $this->assertEquals('varchar', $table->getColumnDefinitions()['name']);
    }

    /**
     * @expectedException PhpDB\Exceptions\UnsupportedTypeException
     * @expectedExceptionMessage Type double is not supported
     */
    public function testInvalidColumnTypeThrowsError()
    {
        new Table('test', ['price'=>'double']);
    }

    /**
     * @expectedException \PhpDB\Exceptions\InvalidNameException
     * @expectedExceptionMessage Column name bad name is invalid
     */
    public function testInvalidColumnNameThrowsError()
    {
        new Table('test', ['bad name'=>'int']);
    }
}

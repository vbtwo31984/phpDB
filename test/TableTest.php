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

    public function testRetrievingDataFromEmptyTableReturnsEmptyArray()
    {
        $table = new Table('test', ['id'=>'int', 'name'=>'varchar']);
        $data = $table->select([]);
        $this->assertCount(0, $data);
    }

    public function testCanInsertAndRetrieveData()
    {
        $table = new Table('test', ['id'=>'int', 'name'=>'varchar']);
        $table->insert(['id'=>1, 'name'=>'John']);
        $data = $table->select([]);
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('name', $data[0]);
        $this->assertEquals(1, $data[0]['id']);
        $this->assertEquals('John', $data[0]['name']);
    }

    public function testCanInsertAndRetrieveMultipleRows()
    {
        $table = new Table('test', ['id'=>'int', 'name'=>'varchar']);
        $table->insert(['id'=>1, 'name'=>'John']);
        $table->insert(['id'=>2, 'name'=>'Jill']);
        $data = $table->select([]);
        $this->assertCount(2, $data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('name', $data[0]);
        $this->assertEquals(1, $data[0]['id']);
        $this->assertEquals('John', $data[0]['name']);
        $this->assertArrayHasKey('id', $data[1]);
        $this->assertArrayHasKey('name', $data[1]);
        $this->assertEquals(2, $data[1]['id']);
        $this->assertEquals('Jill', $data[1]['name']);
    }

    public function testInsertingPartialRowResultsInOtherColumnsSetToNull()
    {
        $table = new Table('test', ['id'=>'int', 'name'=>'varchar']);
        $table->insert(['id'=>1]);
        $data = $table->select([]);
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('name', $data[0]);
        $this->assertEquals(1, $data[0]['id']);
        $this->assertNull($data[0]['name']);
    }

    public function testInsertingIntResultsInConversionToInt()
    {
        $table = new Table('test', ['int1'=> 'int', 'int2'=>'int']);
        $table->insert(['int1'=>'6', 'int2'=>'non-numeric string']);
        $data = $table->select([]);
        $this->assertInternalType('int', $data[0]['int1']);
        $this->assertEquals(6, $data[0]['int1']);
        $this->assertInternalType('int', $data[0]['int2']);
        $this->assertEquals(0, $data[0]['int2']);
    }

    public function testInsertingVarcharResultsInString()
    {
        $table = new Table('test', ['name'=>'varchar']);
        $table->insert(['name'=>123]);
        $data = $table->select([]);
        $this->assertInternalType('string', $data[0]['name']);
        $this->assertEquals('123', $data[0]['name']);
    }

    public function testSelectWithWhere()
    {
        $table = new Table('test', ['id'=>'int', 'name'=>'varchar']);
        $table->insert(['id'=>1, 'name'=>'John']);
        $table->insert(['id'=>2, 'name'=>'Jill']);
        $table->insert(['id'=>3, 'name'=>'John']);
        $data = $table->select(['name'=>'John']);
        $this->assertCount(2, $data);
        $this->assertEquals(1, $data[0]['id']);
        $this->assertEquals(3, $data[1]['id']);
    }
}

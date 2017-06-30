<?php

namespace PhpDB;


use PhpDB\Exceptions\InvalidTableDefinitionException;
use PhpDB\Exceptions\ParseException;

class SyntaxParser
{
    /**
     * @param string $command
     * @return Table the table to be created
     * @throws InvalidTableDefinitionException if column has no data type
     * @throws ParseException if the syntax is invalid
     */
    public static function parseCreateTable($command)
    {
        // remove create table
        $command = trim_leading_string($command, 'create table ');

        // to send to preg functions
        $matches = [];

        // parse out table name - everything up to first (
        preg_match('/^[^(]*/', $command, $matches);
        $tableName = trim($matches[0]);

        // parse out columns - everything in between ( )
        preg_match_all('/\(([^\)]*)\)/', $command, $matches);

        // multiple parentheses found, invalid syntax
        if (count($matches[1]) > 1) {
            throw new ParseException('Create table syntax invalid');
        }

        $columnDefinitionsString = $matches[1][0];
        $columnsWithTypes = explode(',', $columnDefinitionsString);

        $columnDefinitions = [];
        foreach ($columnsWithTypes as $columnWithType) {
            if (strlen($columnWithType) > 0) {
                // regex splits by last space, so everything up to last space is column name, and everything after last space is type
                list($name, $type) = preg_split("/\s+(?=\S*+$)/", $columnWithType);
                $name = trim($name);
                $type = trim($type);

                if (strlen($type) === 0) {
                    throw new InvalidTableDefinitionException("Column $name has no data type");
                }

                $columnDefinitions[$name] = $type;
            }
        }

        return new Table($tableName, $columnDefinitions);
    }

    /**
     * @param string $command
     * @return array key is table name, value is associative array of where clause: $column => $value
     * @throws ParseException if there is syntax error
     */
    public static function parseSelect($command)
    {
        // remove leading select, since we're only supporting select *
        $command = trim_leading_string($command, 'select * from ');

        // to send to preg functions
        $matches = [];

        // parse out table name - everything up to where
        preg_match('/^.+(?=where)/', $command, $matches);
        if (count($matches) > 0) { // had where clause
            $tableName = trim($matches[0]);
        } else { // no where clause, everything is the table name
            $tableName = trim($command);
        }

        // parse out where
        $where = [];
        preg_match('/where(.*)$/', $command, $matches);
        if (count($matches) > 0) {
            $whereClause = $matches[1];
            list($column, $value) = explode('=', $whereClause, 2);
            $column = trim($column);
            // trim out the spaces or ' chars from value
            $value = trim($value, " \t\n\r'");

            if (strlen($column) == 0 || strlen($value) == 0) {
                throw new ParseException('Select syntax invalid');
            }

            $where[$column] = $value;
        }


        return [$tableName => $where];
    }

    /**
     * @param string $command
     * @return array key is table name, value is associative array of data to insert [$column=>$value, $column2=>$value2, ...]
     * @throws ParseException if syntax error
     */
    public static function parseInsert($command)
    {
        // test basic syntax
        if (!preg_match('/^insert into .+ ?\(.+\) ?values ?\(.+\)$/', $command)) {
            throw new ParseException('Insert syntax invalid');
        }

        // remove leading insert
        $command = trim_leading_string($command, 'insert into ');

        // parse out table name - everything up to first (
        preg_match('/^[^(]*/', $command, $matches);
        $tableName = trim($matches[0]);

        // parse out column names and values
        $matches = [];
        preg_match_all('/\((.+)\) ?values/', $command, $matches);
        $columnsString = $matches[1][0];

        preg_match_all('/values ?\((.+)\)$/', $command, $matches);
        $valuesString = $matches[1][0];

        // split by commas
        $columns = str_getcsv($columnsString);
        // trim every column name
        $columns = array_map(function ($column) {
            return trim($column);
        }, $columns);

        // split by commas, better than explode since it also removes enclosing quotes, and takes into account commas inside quotes
        $values = str_getcsv($valuesString, ',', "'");
        // trim every value
        $values = array_map(function ($value) {
            return trim($value);
        }, $values);

        // amount of columns must match to amount of values
        if (count($columns) != count($values)) {
            throw new ParseException('Number of columns and values does not match');
        }

        return [$tableName => array_combine($columns, $values)];
    }
}
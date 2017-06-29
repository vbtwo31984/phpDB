<?php

namespace PhpDB;


use PhpDB\Exceptions\InvalidTableDefinitionException;
use PhpDB\Exceptions\ParseException;

class SyntaxParser
{
    public static function parseCreateTable($string)
    {
        // remove create table
        $string = trim_leading_string($string, 'create table ');

        // to send to preg functions
        $matches = [];

        // parse out table name - everything up to first (
        preg_match('/^[^(]*/', $string, $matches);
        $tableName = trim($matches[0]);

        // parse out columns - everything in between ( )
        preg_match_all('/\(([^\)]*)\)/', $string, $matches);

        // multiple parentheses found, invalid syntax
        if(count($matches[1]) > 1) {
            throw new ParseException('Invalid create table syntax');
        }

        $columnDefinitionsString = $matches[1][0];
        $columnsWithTypes = explode(',', $columnDefinitionsString);

        $columnDefinitions = [];
        foreach($columnsWithTypes as $columnWithType) {
            if(strlen($columnWithType) > 0) {
                // regex splits by last space, so everything up to last space is column name, and everything after last space is type
                list($name, $type) = preg_split("/\s+(?=\S*+$)/", $columnWithType);
                $name = trim($name);
                $type = trim($type);
                if(strlen($type) === 0) {
                    throw new InvalidTableDefinitionException("Column $name has no data type");
                }
                $columnDefinitions[$name] = $type;
            }
        }

        return new Table($tableName, $columnDefinitions);
    }
}
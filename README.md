# PhpDB
PhpDB is an implementation of a simple sql in memory database using PHP. This lives entirely in memory and once you quit, all data is lost.

This supports only a limited subset of SQL, see below for all supported commands.

Requires at least PHP 7.

## To run
```
php db.php
```

## Supported commands
```
list databases
```
Shows a list of created databases
```sql
create database dbname
```
Creates a database with name of dbname
```sql
use dbname
```
Switches the active database to dbname
```sql
list tables
```
Shows a list of created tables in the currently active database
```sql
create table tablename (columnname type, columnname2 type2, ...)
```
Creates a table with name tablename in the currently active database

There are only 2 types supported - int and varchar

Everything you insert into an int column will convert into an integer using PHP's intval function
```
select * from tablename
```
Selects all rows from tablename
```sql
select * from tablename where columnname = value
```
Selects all rows from tablename where the column columnname equals the value provided

Only equals is supported, only one where clause is supported
```sql
quit
```
Quits the database. All data will be lost, as it only lives in memory

## To test
```
cd path/to/project
php path/to/phpunit.phar
```

## To generate coverage report
The latest coverage report is in the git repository under [test/coverage](https://rawgit.com/vbtwo31984/phpDB/master/test/coverage/index.html)

If you want to generate your own report, make sure you have xdebug enabled, and run
```
cd path/to/project
php path/to/phpunit.phar --coverage-html path/to/save/dir
```
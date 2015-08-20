<?php

namespace Library\Database\Drivers;

use Library\Database\Table;

interface IDatabaseDriver
{
    function insert(Table $table, $values);

    function select($tableName);

    function dropAll();
}
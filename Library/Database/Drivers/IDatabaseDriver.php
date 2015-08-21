<?php

namespace Library\Database\Drivers;

interface IDatabaseDriver
{
    function insert($table, array $data);

    function select($table);

    function dropAll();

    function beginTransaction();

    function commit();

    function rollBack();
}
<?php

namespace Library\Database\Drivers;

use Library\Database\Table;

class PdoDatabaseDriver implements IDatabaseDriver
{
    protected $dao;
    protected $database;

    public function __construct($dao, $settings)
    {
        $this->database = $settings['database'];
        $this->dao = new PDO('mysql:host='.$settings['host'].';dbname='.$this->database,
            $settings['username'],
            $settings['password']);

        $this->dao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    function insert(Table $table, $values)
    {

    }

    function dropAll()
    {
        $this->dao->exec('DROP DATABASE '.$this->database);
        $this->dao->exec('CREATE DATABASE '.$this->database);
    }
}
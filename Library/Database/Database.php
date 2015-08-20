<?php

namespace Library\Database;

use Library\Database\Drivers\PdoDatabaseDriver;
use Library\Database\Drivers\RedisDatabaseDriver;
use Library\Config;
use Symfony\Component\Yaml\Exception\RuntimeException;

class Database
{
    protected $driver;

    public function __construct($settings)
    {
        $this->initializeDriver($settings);
    }

    protected function initializeDriver($settings)
    {
        switch ($settings['driver'])
        {
            case 'redis':
                $this->driver = new RedisDatabaseDriver($settings['redis']);
                break;
            case 'mysql':
                $this->driver = new PdoDatabaseDriver($settings['mysql']);
                break;
            default:
                throw new RuntimeException('No database configured.');
                break;
        }
    }

    /* SCHEMA QUERIES */

    public function createTableIfNotExists()
    {
        if ($this->driver instanceof RedisDatabaseDriver)
        {
            return;
        }

        $this->driver->createIfNotExists();
    }

    public function drop($tableName)
    {
        if ($this->driver instanceof RedisDatabaseDriver)
        {
            return;
        }

        $this->driver->drop($tableName);
    }

    public function dropAll()
    {
        $this->driver->dropAll();
    }

    /* QUERIES */

    public function insert(Table $table, $values)
    {
        $this->driver->insert($table, $values);
    }

    public function select($tableName)
    {
        $this->driver->select($tableName);
    }

    /* DEPRECATED */

    public function exec()
    {

    }

    public function prepare()
    {

    }

//    public function query($sql)
//    {
//        $query = $this->dao->prepare($sql);
//        $query->execute();
//        return $query;
//    }

//    public function beginTransaction()
//    {
//        $this->dao->beginTransaction();
//    }
//
//    public function commit()
//    {
//        $this->dao->commit();
//    }
//
//    public function rollBack()
//    {
//        $this->dao->rollBack();
//    }
}

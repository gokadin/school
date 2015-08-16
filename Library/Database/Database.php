<?php

namespace Library\Database;

use Library\Database\Drivers\RedisDatabaseDriver;
use Library\Config;

class Database
{
//    protected $dao;
    protected $driver;

    public function __construct($settings)
    {
//        $this->dao = new PDO($settings['mysql']['driver'].':host='.$settings['mysql']['host'].';dbname='.$settings['mysql']['database'],
//            $settings['mysql']['username'],
//            $settings['mysql']['password']);
//
//        $this->dao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//
        $this->initializeDriver($settings);
    }

    protected function initializeDriver($settings)
    {
        switch ($settings['driver'])
        {
            case 'redis':
                $this->driver = new RedisDatabaseDriver($settings['redis']);
                break;
            default:
                $this->driver = null;
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

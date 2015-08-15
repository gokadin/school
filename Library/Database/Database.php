<?php

namespace Library\Database;

use Library\Database\Drivers\RedisDatabaseDriver;
use Library\Facades\App;
use Symfony\Component\Yaml\Exception\RuntimeException;
use Library\Config;
use PDO;

class Database
{
    protected $dao;
    protected $tables;
    protected $driver;

    public function __construct()
    {
        $settings = require App::basePath().'Config/database.php';

        $this->dao = new PDO($settings['mysql']['driver'].':host='.$settings['mysql']['host'].';dbname='.$settings['mysql']['database'],
            $settings['mysql']['username'],
            $settings['mysql']['password']);

        $this->dao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->tables = new TableBuilder($this);
    }

    protected function initializeDriver($driverName)
    {
        switch ($driverName)
        {
            case 'redis':
                $this->driver = new RedisDatabaseDriver(App::container()->resolveInstance('redis'));
                break;
            default:
                $this->driver = null;
                break;
        }
    }

    public function driver()
    {
        return $this->driver;
    }

    public function dao()
    {
        return $this->dao;
    }

    public function getTable($modelName)
    {
        return $this->tables->getTable($modelName);
    }

    public function table($table)
    {
        if (!is_string($table) || empty($table))
            throw new RuntimeException('Invalid module');

        $table = strtolower($table);
        if (env('APP_ENV') == 'framework_testing')
            $className = '\\Tests\\FrameworkTest\\Models\\'.ucfirst($table);
        else
            $className = '\\Models\\'.ucfirst($table);

        return new $className();
    }

    public function query($sql)
    {
        $query = $this->dao->prepare($sql);
        $query->execute();
        return $query;
    }

    public function exec($sql)
    {
        return $this->dao->exec($sql);
    }

    public function dropAllTables()
    {
        return $this->tables->dropAllTables();
    }

    public function dropTable($tableName)
    {
        return $this->tables->dropTable($tableName);
    }

    public function beginTransaction()
    {
        $this->dao->beginTransaction();
    }

    public function commit()
    {
        $this->dao->commit();
    }

    public function rollBack()
    {
        $this->dao->rollBack();
    }
}

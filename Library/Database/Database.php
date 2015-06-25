<?php namespace Library\Database;

use Symfony\Component\Yaml\Exception\RuntimeException;
use Library\Config;

class Database
{
    protected $dao;
    protected $tables;

    public function __construct()
    {
        $settings = include __DIR__.'/../../Config/database.php';

        $this->dao = new \PDO($settings['mysql']['driver'].':host='.$settings['mysql']['host'].';dbname='.$settings['mysql']['database'],
            $settings['mysql']['username'],
            $settings['mysql']['password']);

        $this->dao->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->tables = new TableBuilder($this);
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
        if (Config::get('frameworkTesting') == 'true')
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

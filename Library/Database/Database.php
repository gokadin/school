<?php namespace Library\Database;

use Symfony\Component\Yaml\Exception\RuntimeException;
use Library\PDOFactory;
use Library\Config;

class Database
{
    protected $dao;
    protected $tables;

    public function __construct()
    {
        if (Config::get('testing') == 'true')
            $this->dao = PDOFactory::testConn();
        else
            $this->dao = PDOFactory::conn();

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
        if (Config::get('testing') == 'true')
            $className = '\\Tests\\FrameworkTest\\Database\\Models\\'.ucfirst($table);
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
}

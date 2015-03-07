<?php namespace Library\Database;

use Symfony\Component\Yaml\Exception\RuntimeException;

class Database
{
    protected $dao;
    protected $tables;

    public function __construct($dao)
    {
        $this->dao = $dao;
        $this->tables = new TableBuilder($this);
    }

    public function dao()
    {
        return $this->dao;
    }

    public function getBlueprint($modelName)
    {
        return $this->tables->getBlueprint($modelName);
    }

    public function table($table)
    {
        if (!is_string($table) || empty($table))
            throw new RuntimeException('Invalid module');

        $table = strtolower($table);
        $className = '\\Models\\'.ucfirst($table);

        return new $className();
    }

    public function query($sql)
    {
        return $this->dao->query($sql);
    }
}

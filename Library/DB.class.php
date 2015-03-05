<?php
namespace Library;

class DB {
    private $dao;

    public function __construct($dao)
    {
        $this->dao = $dao;
    }

    public function dao()
    {
        return $this->dao;
    }

    public function table($tableName)
    {
        if (!is_string($tableName) || empty($tableName)) {
            throw new \InvalidArgumentException('Invalid module');
        }

        $tableName = strtolower($tableName);
        $className = '\\Models\\'.ucfirst($tableName);

        return new $className($this->dao);
    }
}
?>
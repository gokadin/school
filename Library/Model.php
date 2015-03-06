<?php namespace Library;

use PDOException;
use PDO;

class Model
{
    protected $dao;
    protected $tableName;
    protected $entityName;

    public function __construct($dao, $tableName)
    {
        $this->dao = $dao;
        $this->tableName = $tableName;
        $this->entityName = 'Entity';
    }

    protected function setEntityName($name)
    {
        $this->entityName = '\\Entities\\'.ucfirst($name);
    }

    public function query($sql)
    {
        return $this->dao->query($sql);
    }

    public function exists($var, $value)
    {
        $sql = 'SELECT COUNT(*) FROM '.$this->tableName.' WHERE '.$var.' = '.$value;

        try
        {
            $result = $this->dao->query($sql);
            return $result->rowCount() > 0;
        }
        catch (PDOException $e)
        {
            return false;
        }
    }

    public function select()
    {
        $sql = 'SELECT * FROM '.$this->tableName;

        $q = $this->dao->prepare($sql);

        $q->execute();
        $q->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->entityName);
        $list = $q->fetchAll();
        $q->closeCursor();

        return $list;
    }
}

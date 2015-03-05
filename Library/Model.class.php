<?php namespace Library;

use PDOException;
use PDO;

class Model
{
    protected $tableName;
    protected $dao;

    public function __construct($dao)
    {
        $this->dao = $dao;
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
        $q->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Entity');
        $list = $q->fetchAll();
        $q->closeCursor();

        return $list;
    }
}

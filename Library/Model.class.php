<?php
namespace Library;

abstract class Model {
    protected $dao = null;
    protected $table_name = null;

    public function __construct($dao, $table_name)
    {
        $this->dao = $dao;
        $this->table_name = $table_name;
    }

    abstract function init();

    public function query($sql)
    {
        return $this->dao->query($sql);
    }

    public function exists($var, $value)
    {
        $sql = 'SELECT COUNT(*) FROM '.$this->table_name.' WHERE '.$var.' = '.$value;

        try
        {
            $result = $this->dao->query($sql);
            return $result->rowCount() > 0;
        }
        catch (\PDOException $e)
        {
            return false;
        }
    }

    public function select()
    {
        $sql = 'SELECT * FROM '.$this->table_name;

        $q = $this->dao->prepare($sql);

        //$q->bindValue(':table', $this->table_name);

        $q->execute();
        $q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Entity');
        $list = $q->fetchAll();
        $q->closeCursor();

        return $list;
    }
}
?>
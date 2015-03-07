<?php namespace Library\Database;

class Query
{
    protected $dao;
    protected $table;

    public function __construct($dao)
    {
        $this->dao = $dao;
    }

    protected function buildInsert(array $names)
    {
        $sql = 'INSERT INTO '.$this->table.' ('.implode(',', $names).')';
        $sql .= ' VALUES('.$this->getQuestionMarks(sizeof($names)).')';
        return $sql;
    }

    protected function buildUpdate(array $names)
    {
        $sql = 'UPDATE '.$this->table.' SET ';
        foreach ($names as &$name)
            $name .= '=?';
        $sql .= implode(', ', $names);
        return $sql;
    }

    protected function getQuestionMarks($count)
    {
        $str = '?';
        for ($i = 0; $i < $count - 1; $i++)
            $str .= ', ?';
        return $str;
    }

    protected function prepareAndExecute($str, $values)
    {
        $stmt = $this->dao->prepare($str);
        return $stmt->execute($values);
    }

    protected function lastInsertId()
    {
        return $this->dao->lastInsertId();
    }
}
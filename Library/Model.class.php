<?php namespace Library;

class Model
{
    protected $tableName;

    public function query($sql)
    {
        return DB::dao()->query($sql);
    }

    public function exists($var, $value)
    {
        $sql = 'SELECT COUNT(*) FROM '.$this->tableName.' WHERE '.$var.' = '.$value;

        try
        {
            $result = DB::dao()->query($sql);
            return $result->rowCount() > 0;
        }
        catch (\PDOException $e)
        {
            return false;
        }
    }

    public function select()
    {
        $sql = 'SELECT * FROM '.$this->tableName;

        $q = DB::dao()->prepare($sql);

        $q->execute();
        $q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Entity');
        $list = $q->fetchAll();
        $q->closeCursor();

        return $list;
    }
}
?>
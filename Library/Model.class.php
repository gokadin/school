<?php
namespace Library;

class Model {

    public static function tableName()
    {
        return substr(strstr(strtolower(get_called_class()), '\\'), 1);
    }

    public static function query($sql)
    {
        return DB::dao()->query($sql);
    }

    public static function exists($var, $value)
    {
        $sql = 'SELECT COUNT(*) FROM '.self::tableName().' WHERE '.$var.' = '.$value;

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

    public static function select()
    {
        $sql = 'SELECT * FROM '.self::tableName();

        $q = DB::dao()->prepare($sql);

        $q->execute();
        $q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Entity');
        $list = $q->fetchAll();
        $q->closeCursor();

        return $list;
    }
}
?>
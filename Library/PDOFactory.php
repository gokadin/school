<?php namespace Library;

class PDOFactory
{
    public static function conn()
    {
        $db = new \PDO('mysql:host=localhost;dbname=School', 'root', 'f10ygs87');
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $db;
    }

    public static function testConn()
    {
        $db = new \PDO('mysql:host=localhost;dbname=SchoolTest', 'root', 'f10ygs87');
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $db;
    }
}

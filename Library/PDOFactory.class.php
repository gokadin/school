<?php
namespace Library;

class PDOFactory {
    public static function get_mysql_connexion() {
        $db = new \PDO('mysql:host=localhost;dbname=School', 'root', 'f10ygs87');
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $db;
    }
}
?>
<?php
namespace Library;

class DB {
    private static $dao;

    public static function init($dao)
    {
        self::$dao = $dao;
    }

    public static function table($tableName)
    {
        if (!is_string($tableName) || empty($tableName)) {
            throw new \InvalidArgumentException('Invalid module');
        }

        $tableName = strtolower($tableName);
        $className = '\\Models\\'.ucfirst($tableName);

        return null;
    }

    public static function dao()
    {
        return self::$dao;
    }
}
?>
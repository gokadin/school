<?php
namespace Library;

class DB {
    private static $dao;
    private static $loaded_tables = array();

    public static function init($dao)
    {
        self::$dao = $dao;
    }

    public static function table($table_name)
    {
        if (!is_string($table_name) || empty($table_name)) {
            throw new \InvalidArgumentException('Invalid module');
        }

        $table_name = strtolower($table_name);

        if (isset(self::$loaded_tables[$table_name])) {
            return self::$loaded_tables[$table_name];
        }

        $class_name = '\\Models\\'.ucfirst($table_name);
        self::$loaded_tables[$table_name] = new $class_name(self::$dao, $table_name);
        self::$loaded_tables[$table_name]->init();

        return self::$loaded_tables[$table_name];
    }
}
?>
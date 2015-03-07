<?php namespace Library\Database;

interface ModelQueryContract
{
    public function save();

    //public function touch();

    //public static function where(array $args);

    //public static function find($id);

    public static function exists($var, $value);
}
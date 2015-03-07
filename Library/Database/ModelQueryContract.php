<?php namespace Library\Database;

interface ModelQueryContract
{
    public function save();

    public function delete();

    public function touch();

    public static function create(array $values);

    public static function exists($var, $value);

    public static function where($var, $operator, $value, $link = 'AND');

    public static function find($id);
}

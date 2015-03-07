<?php namespace Library\Database;

interface QueryContract
{
    public function create($values);

    public function update($values);

    public function delete();

    public function where($var, $operator, $value, $link = 'AND');

    public function orWhere($var, $operator, $value);

    public function exists($var, $value);

    public function get($values = null);
}
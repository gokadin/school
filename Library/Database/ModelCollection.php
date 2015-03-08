<?php namespace Library\Database;

class ModelCollection
{
    protected $items = array();

    public function __construct($items = array())
    {
        $this->items = (array) $items;
    }

    public function all()
    {
        return $this->items;
    }

    public function first($default = null)
    {
        return $this->count() > 0 ? reset($this->items) : $default;
    }

    public function last($default = null)
    {
        return $this->count() > 0 ? end($this->items) : $default;
    }

    public function count()
    {
        return count($this->items);
    }

    public function isEmpty()
    {
        return empty($this->items);
    }
}
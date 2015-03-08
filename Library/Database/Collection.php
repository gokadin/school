<?php namespace Library\Database;

class Collection implements Arrayable
{
    protected $items = array();

    public function __construct($items = array())
    {
        $items = is_null($items) ? [] : $this->getArrayableItems($items);
        $this->items = (array) $items;
    }

    public function all()
    {
        return $this->items;
    }

    public function toArray()
    {
        return array_map(function($value)
        {
            return $value->toArray();
        }, $this->items);
    }

    protected function getArrayableItems($items)
    {
        if ($items instanceof Collection)
        {
            $items = $items->all();
        }
        elseif ($items instanceof Arrayable)
        {
            $items = $items->toArray();
        }

        return $items;
    }
}
<?php namespace Library\Database;

class Blueprint
{
    protected $modelName;
    protected $columns;

    public function __construct($modelName)
    {
        $this->modelName = $modelName;
        $this->columns = array();
    }

    public function increments($name)
    {
        return $this->columns[] = new Column($name, 'int', 11);
    }

    public function integer($name, $size = 11)
    {
        return $this->columns[] = new Column($name, 'int', $size);
    }

    public function double($name, $size = 11)
    {
        return $this->columns[] = new Column($name, 'double', $size);
    }

    public function string($name, $size = 50)
    {
        return $this->columns[] = new Column($name, 'string', $size);
    }

    public function boolean($name)
    {
        return $this->columns[] = new Column($name, 'boolean');
    }

    public function timestamps()
    {
        $this->columns[] = new Column('updated_at', 'datetime');
        $this->columns[] = new Column('created_at', 'datetime');
    }

    public function modelName()
    {
        return $this->modelName;
    }

    public function columns()
    {
        return $this->columns;
    }
}
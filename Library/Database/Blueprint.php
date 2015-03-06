<?php namespace Library\Database;

class Blueprint
{
    protected $modelName;
    protected $table;
    protected $columns;

    public function __construct($modelName)
    {
        $this->modelName = $modelName;
        $this->columns = array();
    }

    public function setTable($name)
    {
        $this->table = $name;
    }

    public function increments($name)
    {
        $column = new Column($name, 'integer', 11);
        $column->primaryKey();
        return $this->columns[] = $column;
    }

    public function integer($name, $size = 11)
    {
        return $this->columns[] = new Column($name, 'integer', $size);
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
        return $this->columns[] = new Column($name, 'boolean', 1);
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

    public function table()
    {
        return $this->table;
    }

    public function columns()
    {
        return $this->columns;
    }
}
<?php namespace Library\Database;

class Table
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $modelName;
    protected $tableName;
    protected $columns;

    public function __construct($modelName)
    {
        $this->modelName = $modelName;
        $this->columns = array();
    }

    public function setTable($name)
    {
        $this->tableName = $name;
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

    public function decimal($name, $size = 11, $precision = 2)
    {
        $column = new Column($name, 'decimal', $size);
        $column->precision($precision);
        return $this->columns[] = $column;
    }

    public function string($name, $size = 50)
    {
        return $this->columns[] = new Column($name, 'string', $size);
    }

    public function text($name)
    {
        return $this->columns[] = new Column($name, 'text', 0);
    }

    public function boolean($name)
    {
        return $this->columns[] = new Column($name, 'boolean', 1);
    }
    
    public function datetime($name)
    {
        return $this->columns[] = new Column($name, 'datetime', 0);
    }

    public function timestamps()
    {
        $this->columns[] = new Column(self::UPDATED_AT, 'datetime');
        $this->columns[] = new Column(self::CREATED_AT, 'datetime');
    }

    /* ACCESSORS */

    public function modelName()
    {
        return $this->modelName;
    }

    public function tableName()
    {
        return $this->tableName;
    }

    public function columns()
    {
        return $this->columns;
    }

    public function hasColumn($name)
    {
        foreach ($this->columns as $column)
        {
            if ($column->getName() == $name)
                return true;
        }

        return false;
    }

    public function hasTimestamps()
    {
        return $this->hasColumn(self::UPDATED_AT) && $this->hasColumn((self::CREATED_AT));
    }
}
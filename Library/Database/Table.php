<?php namespace Library\Database;

class Table
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const META_ID = 'meta_id';
    const META_TYPE = 'meta_type';

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

    public function decimal($name, $size = 11)
    {
        return $this->columns[] = new Column($name, 'decimal', $size);
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

    public function meta()
    {
        $this->columns[] = new Column('meta_id', 'integer');
        $this->columns[] = new Column('meta_type', 'string', 32);
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

    public function isMeta()
    {
        if ($this->hasColumn(self::META_ID) && $this->hasColumn(self::META_TYPE))
            return true;

        return false;
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
        return $this->hasColumn('updated_at') && $this->hasColumn(('created_at'));
    }
}
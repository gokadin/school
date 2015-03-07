<?php namespace Library\Database;

use Library\Facades\DB;
use Symfony\Component\Yaml\Exception\RuntimeException;

class Model extends Query
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $vars = array();
    protected $table;
    protected $primaryKey;
    protected $columns;

    public function __construct(array $data = array())
    {
        parent::__construct(DB::dao());

        $modelName = get_called_class();
        $modelName = strtolower(substr($modelName, strrpos($modelName, '\\') + 1));
        $blueprint = DB::getBlueprint($modelName);
        $this->table = $blueprint->table();
        foreach ($blueprint->columns() as $column)
        {
            if ($column->isPrimaryKey())
                $this->primaryKey = $column;
            else
                $this->columns[] = $column;
        }

        foreach ($data as $var => $value)
            $this->__set($var, $value);
    }

    public function __set($var, $value)
    {
        if ($var == $this->primaryKey)
            throw new RuntimeException('Cannot set primary key to model');
        else
            $this->vars[$var] = $value;
    }

    public function __get($var)
    {
        if (isset($this->vars[$var]))
            return $this->vars[$var];
    }

    public function exists()
    {
        return isset($this->vars[$this->primaryKey]);
    }

    public function save()
    {
        if ($this->exists())
            return $this->update();
        else
            return $this->insert();
    }

    protected function insert()
    {
        if (!isset($this->vars['updated_at']) && $this->blueprint->hasTimestamps())
            $this->vars['updated_at'] = 'somedate';
        if (!isset($this->vars['created_at']) && $this->blueprint->hasTimestamps())
            $this->vars['created_at'] = 'somedate';

        $sql = 'INSERT INTO '.$this->blueprint->table().' (';
        foreach ($this->blueprint->columns() as $column)
        {
            if (isset($this->vars[$column->getName()]))
            {
                $sql .= $column->getName().', ';
            }
        }
        $sql = substr($sql, 0, -2);
        $sql .= ') VALUES(\'\', ';

        foreach ($this->blueprint->columns() as $column)
        {
            if (isset($this->vars[$column->getName()]))
            {
                if (is_string($this->vars[$column->getName()]))
                    $sql .= '\''.$this->vars[$column->getName()].'\', ';
                else
                    $sql .= $this->vars[$column->getName()].', ';
            }
        }

        $sql = substr($sql, 0, -2);
        $sql .= ')';

        return $sql;//$this->dao->query($sql);
    }

    protected function update()
    {
        $sql = 'UPDATE '.$this->blueprint->table().' SET ';
        foreach ($this->blueprint->columns() as $column)
        {
            if (isset($this->vars[$column->getName()]))
            {
                $sql .= $column->getName().'='.$this->vars[$column->getName()].', ';
            }
        }
        $sql = substr($sql, 0, -2);

        $sql .= ' WHERE '.$this->blueprint->getPrimaryKey()->getName().'='
                .$this->vars[$this->blueprint->getPrimaryKey()->getName()];

        return $sql;//$this->dao->query($sql);
    }
}

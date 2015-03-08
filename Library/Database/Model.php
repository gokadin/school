<?php namespace Library\Database;

use Library\Facades\DB;
use Symfony\Component\Yaml\Exception\RuntimeException;
use Carbon\Carbon;

class Model implements ModelQueryContract
{
    private $query;
    protected $vars = array();
    protected $table;
    protected $modelName;
    protected $primaryKey;
    private $columns;
    protected $columnNames;
    private $hasTimestamps;

    public function __construct(array $data = array())
    {
        $modelName = get_called_class();
        $this->modelName = strtolower(substr($modelName, strrpos($modelName, '\\') + 1));
        $blueprint = DB::getBlueprint($this->modelName);
        $this->table = $blueprint->table();
        $this->hasTimestamps = $blueprint->hasTimestamps();
        foreach ($blueprint->columns() as $column)
        {
            if ($column->isPrimaryKey())
                $this->primaryKey = $column->getName();
            else
            {
                $this->columns[] = $column;
                $this->columnNames[] = $column->getName();
            }
        }

        foreach ($data as $key => $value)
        {
            $this->__set($key, $value);
        }

        $this->query = new Query($this);
    }

    public function tableName()
    {
        return $this->table;
    }

    public function modelName()
    {
        return $this->modelName;
    }

    public function columnNames()
    {
        return $this->columnNames;
    }

    public function primaryKey()
    {
        return $this->primaryKey;
    }

    public function hasTimestamps()
    {
        return $this->hasTimestamps;
    }

    public function __set($var, $value)
    {
        $this->vars[$var] = $value;
    }

    public function __get($var)
    {
        if (isset($this->vars[$var]))
            return $this->vars[$var];
    }

    public function save()
    {
        if ($this->isMissingPrimaryKey())
            return $this->insert();
        else
            return $this->update();
    }

    private function update()
    {
        if ($this->isMissingRequiredColumn())
        {
            throw new RuntimeException('A required column is missing from table '.$this->table);
            return false;
        }

        $values = array();

        foreach ($this->vars as $key => $var)
        {
            if ($this->hasColumn($key))
                $values[$key] = $var;
        }

        return $this->query->update($values) > 0;
    }

    public function touch()
    {
        if ($this->isMissingPrimaryKey()) {
            throw new RuntimeException('Cannot delete model, it was not yet created.');
            return false;
        }

        if (!$this->hasTimestamps)
        {
            throw new RuntimeException('Cannot perform touch. Timestamps are disabled or not present.');
            return false;
        }

        $this->vars[QueryBuilder::UPDATED_AT] = Carbon::now();

        return $this->update() > 0;
    }

    public function delete()
    {
        if ($this->isMissingPrimaryKey())
        {
            throw new RuntimeException('Cannot delete model, it was not yet created.');
            return false;
        }

        return $this->query->delete() > 0;
    }

    private function insert()
    {
        if ($this->isMissingRequiredColumn())
        {
            throw new RuntimeException('A required column is missing from table '.$this->table);
            return false;
        }

        $values = array();

        foreach ($this->vars as $key => $var)
        {
            if ($this->hasColumn($key))
                $values[$key] = $var;
        }

        $result = $this->query->create($values);

        if (!$result)
            return false;

        $this->hydrate($result);
        return true;
    }

    public static function create(array $values)
    {
        if ($values == null)
        {
            throw new RuntimeException('Values cannot be empty when creating a new model.');
            return null;
        }

        $instance = new static;

        foreach ($values as $key => $value)
        {
            if (!$instance->hasColumn($key))
            {
                throw new RuntimeException('Column '.$key.' does not exist in table '.$instance->tableName());
                return null;
            }

            $instance->$key = $value;
        }

        if ($instance->isMissingRequiredColumn())
        {
            throw new RuntimeException('A required column is missing from table '.$instance->tableName());
            return null;
        }

        $query = new Query($instance);
        return $query->create($values);
    }

    protected function hydrate($otherModel)
    {
        foreach ($otherModel->vars as $key => $value)
            $this->$key = $value;
    }

    public static function exists($var, $value)
    {
        $instance = new static;
        $query = new Query($instance);
        return $query->exists($var, $value);
    }

    public static function where($var, $operator, $value, $link = 'AND')
    {
        $instance = new static;
        $query = new Query($instance);
        return $query->where($var, $operator, $value, $link);
    }

    public static function find($id)
    {
        $instance = new static;
        $query = new Query($instance);
        return $query->where($instance->primaryKey, '=', $id)->get();
    }

    protected function getValuesArray($names)
    {
        $values = array();

        foreach ($names as $name)
        {
            if (isset($this->vars[$name]))
                $values[] = $this->vars[$name];
            else if ($name == self::CREATED_AT || $name == self::UPDATED_AT)
                $values[] = Carbon::now();
            else
                $values[] = null;
        }

        return $values;
    }

    public function hasColumn($name)
    {
        foreach ($this->columnNames as $columnName)
        {
            if ($columnName === $name)
                return true;
        }

        return false;
    }

    public function isMissingRequiredColumn()
    {
        foreach ($this->columns as $column)
        {
            if (!$column->isRequired() && !isset($this->vars[$column->getName()]))
            {
                echo $column->getName();
                return true;
            }
        }

        return false;
    }

    public function isMissingPrimaryKey()
    {
        return !isset($this->vars[$this->primaryKey]);
    }
}

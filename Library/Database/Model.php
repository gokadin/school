<?php namespace Library\Database;

use Library\Facades\DB;
use Symfony\Component\Yaml\Exception\RuntimeException;
use Carbon\Carbon;

class Model extends Query implements ModelQueryContract
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $vars = array();
    protected $primaryKey;
    protected $columns;
    protected $columnNames;
    protected $hasTimestamps;

    public function __construct(array $data = array())
    {
        parent::__construct(DB::dao());

        $modelName = get_called_class();
        $modelName = strtolower(substr($modelName, strrpos($modelName, '\\') + 1));
        $blueprint = DB::getBlueprint($modelName);
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

    public function isNew()
    {
        return !isset($this->vars[$this->primaryKey]);
    }

    public function save()
    {
        if ($this->isNew())
            return $this->insert();
        else
            return $this->update();
    }

    protected function validateInsert()
    {
        foreach ($this->columns as $column)
        {
            if (!$column->isNullable() &&
                $column->getName() != self::CREATED_AT &&
                $column->getName() != self::UPDATED_AT)
            {
                if (!isset($this->vars[$column->getName()]))
                    return false;
            }
        }

        return true;
    }

    protected function insert()
    {
        if (!$this->validateInsert())
        {
            throw new RuntimeException('Missing required fields for insert.');
            return false;
        }

        $str = $this->buildInsert($this->columnNames);
        if ($this->prepareAndExecute($str, $this->getValuesArray($this->columnNames)))
        {
            $this->vars[$this->primaryKey] = $this->lastInsertId();
            return true;
        }

        return false;
    }

    protected function update()
    {
        $names = array();
        foreach ($this->columnNames as $columnName)
        {
            if ($columnName == self::CREATED_AT)
                continue;

            $names[] = $columnName;
        }

        $str = $this->buildUpdate($names);
        return $this->prepareAndExecute($str, $this->getValuesArray($names));
    }

    public static function exists($var, $value)
    {
        $instance = new static;
        return $instance->instanceExists($var, $value);
    }

    protected function instanceExists($var, $value)
    {
        $q = $this->dao->prepare('SELECT '.$this->primaryKey.' FROM '.$this->table.' WHERE '.$var.' = :value');
        if ($q->execute(array(':value' => $value)))
            return $q->rowCount();

        return false;
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
}

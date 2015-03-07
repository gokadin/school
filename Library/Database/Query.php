<?php namespace Library\Database;

use Library\Facades\DB;
use Symfony\Component\Yaml\Exception\RuntimeException;

class Query extends QueryBuilder implements QueryContract
{
    const SELECT = 'select';

    protected $dao;
    protected $model;
    protected $command;
    protected $selectValues = array();

    protected $wheres;

    public function __construct($model, $command = '')
    {
        $this->dao = DB::dao();
        parent::__construct($this->dao, $model);
        $this->model = $model;
        $this->command = $command;

        $this->wheres = array();
    }

    public function create($values)
    {
        return $this->buildInsert($values);
    }

    public function update($values)
    {
        $primaryKey = $this->model->primaryKey();

        return $this->buildUpdate($values)
            .$this->buildWhere([[
                'var' => $primaryKey,
                'operator' => '=',
                'value' => $this->model->$primaryKey]]);
    }

    public function delete()
    {
        $primarykey = $this->model->primaryKey();

        return $this->buildDelete()
            .$this->buildWhere([[
                'var' => $primarykey,
                'operator' => '=',
                'value' => $this->model->$primarykey]]);
    }

    public function exists($var, $value)
    {
        $q = $this->dao->prepare('SELECT '.$this->model->primaryKey.' FROM '.$this->model->table.' WHERE '.$var.' = :value');
        if ($q->execute(array(':value' => $value)))
            return $q->rowCount();

        return false;
    }

    public function where($var, $operator, $value, $link = 'AND')
    {
        if (!$this->columnExists($var))
        {
            throw new RuntimeException('Column '.$var.' does not exist in table '.$this->model->table);
            return $this;
        }

        $this->wheres[] = compact('var', 'operator', 'value', 'link');
        return $this;
    }

    public function orWhere($var, $operator, $value)
    {
        return $this->where($var, $operator, $value, 'OR');
    }

    public function addValues($values)
    {
        if (is_array($values))
        {
            foreach ($values as $value)
            {
                if (!$this->columnExists($value))
                {
                    throw new RuntimeException('Column '.$value.' does not exist in table '.$this->model->table);
                    return;
                }
            }

            $this->selectValues = $values;
            return $this;
        }

        if (!$this->columnExists($values))
        {
            throw new RuntimeException('Column '.$values.' does not exist in table '.$this->model->table);
            return;
        }

        $this->selectValues[] = $values;
        return $this;
    }

    protected function columnExists($name)
    {
        if ($this->model->primaryKey() == $name)
            return true;

        foreach ($this->model->columnNames() as $columnName)
        {
            if ($name == $columnName)
                return true;
        }

        return false;
    }

    public function get($values = null)
    {
        if ($values != null)
            $this->addValues($values);

        switch ($this->command)
        {
            case 'select':
                return $this->buildSelect($this->selectValues, $this->wheres);
            default:
                throw new RuntimeException('Invalid query builder command.');
                return null;
        }
    }
}
<?php namespace Library\Database;

use Library\Facades\DB;
use Symfony\Component\Yaml\Exception\RuntimeException;
use PDO;

class Query extends QueryBuilder implements QueryContract
{
    const MODEL_DIRECTORY = '\\Models\\';

    protected $dao;
    protected $model;
    protected $command;
    protected $selectValues = array();

    protected $wheres;

    public function __construct($model)
    {
        $this->dao = DB::dao();
        parent::__construct($this->dao, $model);
        $this->model = $model;

        $this->wheres = array();
    }

    public function lastInsertId()
    {
        return $this->dao->lastInsertId();
    }

    public function create($values)
    {
        $str = $this->buildInsert($values);

        if (!$this->dao->exec($str))
            return false;
        else
            return $this->where($this->model->primaryKey(), '=', $this->lastInsertId())->get();
    }

    public function update($values)
    {
        $primaryKey = $this->model->primaryKey();

        $str = $this->buildUpdate($values)
            .$this->buildWhere([[
                'var' => $primaryKey,
                'operator' => '=',
                'value' => $this->model->$primaryKey]]);

        return $this->dao->exec($str);
    }

    public function delete()
    {
        $primarykey = $this->model->primaryKey();

        $str = $this->buildDelete()
            .$this->buildWhere([[
                'var' => $primarykey,
                'operator' => '=',
                'value' => $this->model->$primarykey]]);

        return $this->dao->exec($str);
    }

    public function exists($var, $value)
    {
        $q = $this->dao->prepare('SELECT '.$this->model->primaryKey().' FROM '.$this->model->tableName().' WHERE '.$var.' = :value');
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

        if (trim($values) == '*')
            return;

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

        $str = $this->buildSelect($this->selectValues, $this->wheres);

        $result = $this->dao->prepare($str);
        $result->execute();

        if ($this->selectValues == null)
        {
            $result->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, self::MODEL_DIRECTORY.$this->model->modelName());
            $list = $result->fetchAll();
        }
        else if (sizeof($this->selectValues) > 1)
            $list = $result->fetchAll(PDO::FETCH_ASSOC);
        else
            $list = $result->fetchAll(PDO::FETCH_COLUMN, 0);

        $result->closeCursor();

        if (sizeof($list) == 0)
            return null;

        if (is_array($list) && sizeof($list) == 1)
            return $list[0];

        return $list;
    }
}
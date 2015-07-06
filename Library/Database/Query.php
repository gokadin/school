<?php namespace Library\Database;

use Library\Facades\DB;
use PDO;
use Carbon\Carbon;

class Query extends QueryBuilder implements QueryContract
{
    protected $dao;
    protected $model;
    protected $baseQuery;
    protected $selectValues = array();
    protected $baseSelectValues = array();
    protected $wheres = array();
    protected $baseWheres = array();
    protected $polymorphicQueryLink;
    protected $joins = array();

    public function __construct($model)
    {
        $this->dao = DB::dao();
        parent::__construct($this->dao, $model);
        $this->model = $model;
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

        return $this->lastInsertId();
    }

    public function update($values)
    {
        $str = $this->buildUpdate($values)
            .$this->buildWheres([[
                'var' => $this->model->primaryKeyName(),
                'operator' => '=',
                'value' => $this->model->primaryKeyValue()
            ]]);

        return $this->dao->exec($str) > 0;
    }

    public function touch()
    {
        $updatedAtName = Table::UPDATED_AT;
        $values = array();
        $values[Table::UPDATED_AT] = $this->model->$updatedAtName;

        $str = $this->buildUpdate($values)
            .$this->buildWheres([[
                'var' => $this->model->primaryKeyName(),
                'operator' => '=',
                'value' => $this->model->primaryKeyValue()
            ]]);

        return $this->dao->exec($str) > 0;
    }

    public function delete()
    {
        $str = $this->buildDelete();

        $str .= $this->buildWheres($this->wheres);

        return $this->dao->exec($str) > 0;
    }

    public function exists($var, $value)
    {
        $q = $this->dao->prepare('SELECT '.$this->model->primaryKeyName().' FROM '.$this->model->tableName().' WHERE '.$var.' = :value');
        if ($q->execute(array(':value' => $value)))
            return $q->rowCount() > 0;

        return false;
    }

    public function join($joinTableName, $on = null, $operator = '=', $to = null)
    {
        $this->joins[] = compact('joinTableName', 'on', 'operator', 'to');
        return $this;
    }

    public function where($var, $operator, $value, $link = 'AND')
    {
        if (trim($operator) != 'in')
        {
            if (substr($value, 0, 1) != '\'' && substr($value, -1) != '\'')
                $value = '\''.$value.'\'';
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
        if ($values == null)
            return;

        if (is_array($values))
        {
            $this->selectValues = array_merge($this->selectValues, $values);
            return;
        }

        $this->selectValues[] = $values;
    }

    public function get($values = null)
    {
        $this->addValues($values);
        return $this->select();
    }

    private function select()
    {
        $str = $this->buildSelect($this->selectValues);

        if (sizeof($this->joins) > 0)
            $str .= $this->buildJoins($this->joins);

        if (sizeof($this->wheres) > 0)
            $str .= $this->buildWheres($this->wheres);

        $result = $this->dao->prepare($str);
        $result->execute();

        if (sizeof($this->selectValues) == 0)
        {
            $result->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->model->modelDirectory().$this->model->modelName());
            $list = $result->fetchAll();
        }
        else if (sizeof($this->selectValues) > 1)
            $list = $result->fetchAll(PDO::FETCH_ASSOC);
        else
            $list = $result->fetchAll(PDO::FETCH_COLUMN, 0);

        $result->closeCursor();

        if (sizeof($this->selectValues) == 0)
            return new ModelCollection($list);

        return $list;
    }
}
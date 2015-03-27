<?php namespace Library\Database;

use Library\Facades\DB;
use PDO;

class Query extends QueryBuilder implements QueryContract
{
    protected $dao;
    protected $model;
    protected $baseQuery;
    protected $selectValues = array();

    protected $wheres;
    protected $joins;

    public function __construct($model)
    {
        $this->dao = DB::dao();
        parent::__construct($this->dao, $model);
        $this->model = $model;
        $this->wheres = array();
        $this->joins = array();
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

        $this->where($this->model->primaryKey(), '=', $this->lastInsertId());
        return $this->get();
    }

    public function update($values)
    {
        $primaryKey = $this->model->primaryKey();

        $str = $this->buildUpdate($values)
            .$this->buildWheres([[
                'var' => $primaryKey,
                'operator' => '=',
                'value' => $this->model->$primaryKey]]);

        return $this->dao->exec($str);
    }

    public function delete()
    {
        $primarykey = $this->model->primaryKey();

        $str = $this->buildDelete()
            .$this->buildWheres([[
                'var' => $primarykey,
                'operator' => '=',
                'value' => $this->model->$primarykey]]);

        return $this->dao->exec($str);
    }

    public function exists($var, $value)
    {
        $q = $this->dao->prepare('SELECT '.$this->model->primaryKey().' FROM '.$this->model->tableName().' WHERE '.$var.' = :value');
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
        if (is_array($values))
        {
            $this->selectValues = $values;
            return;
        }

        if (trim($values) == '*')
            return;

        $this->selectValues[] = $values;
    }

    public function get($values = null)
    {
        if (!$this->model->hasBaseModel() || sizeof($this->wheres) == 0)
            return $this->select($values);

        $pWheres = array();
        $bWheres = array();
        foreach ($this->wheres as $where)
        {
            if ($this->model->hasColumn($where['var']))
            {
                $pWheres[] = $where;
                continue;
            }

            if ($this->model->baseModel()->hasColumn($where['var']))
                $bWheres[] = $where;
        }

        $pValues = array();
        $bValues = array();
        if ($values != null)
        {
            if (!is_array($values))
            {
                if ($this->model->hasColumn($values))
                    $pValues[] = $values;
                else if ($this->model->baseModel->hasColumn($values))
                    $bValues[] = $values;
            }
            else
            {
                foreach ($values as $value)
                {
                    if ($this->model->hasColumn($value))
                        $pValues[] = $value;
                    else if ($this->model->baseModel()->hasColumn($value))
                        $bValues[] = $value;
                }
            }

            if (sizeof($pValues) == 0)
                $pValues = null;
            if (sizeof($bValues) == 0)
                $bValues = null;
        }

        $keepPrimaryKey = false;
        if ($pValues != null && !in_array($this->model->primaryKey(), $pValues))
            $pValues[] = $this->model->primaryKey();
        else if ($pValues != null)
            $keepPrimaryKey = true;

        $this->wheres = $pWheres;
        $pResults = sizeof($pWheres) > 0
            ? $this->select($pValues)
            : null;

        $bResults = null;
        $keepMetaId = false;
        if (sizeof($bWheres) > 0)
        {
            $bWheres[] = [
                'var' => Table::META_TYPE,
                'operator' => '=',
                'value' => $this->model->modelName(),
                'link' => 'AND'
            ];

            $keepMetaId = false;
            if ($bValues != null && !in_array(Table::META_ID, $bValues))
                $bValues[] = Table::META_ID;
            else if ($bValues != null)
                $keepMetaId = true;

            $baseQuery = $this->model->baseModel()->where($bWheres[0]['var'],
                $bWheres[0]['operator'],
                $bWheres[0]['value'],
                $bWheres[0]['link']);
            for ($i = 1; $i < sizeof($bWheres); $i++)
                $baseQuery->where($bWheres[$i]['var'],
                    $bWheres[$i]['operator'],
                    $bWheres[$i]['value'],
                    $bWheres[$i]['link']);

            $bResults = $baseQuery->get($bValues);
        }

        return $this->merge($pResults, $bResults, $keepPrimaryKey, $keepMetaId);
    }

    private function select($values = null)
    {
        if ($values != null)
            $this->addValues($values);

        $str = $this->buildSelect($this->selectValues);

        if ($this->joins != null && sizeof($this->joins) > 0)
            $str .= $this->buildJoins($this->joins);

        if ($this->wheres != null && sizeof($this->wheres) > 0)
            $str .= $this->buildWheres($this->wheres);

        $result = $this->dao->prepare($str);
        $result->execute();

        if ($this->selectValues == null)
        {
            $result->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->model->modelDirectory().$this->model->modelName());
            $list = $result->fetchAll();
        }
        else if (sizeof($this->selectValues) > 1)
            $list = $result->fetchAll(PDO::FETCH_ASSOC);
        else
            $list = $result->fetchAll(PDO::FETCH_COLUMN, 0);

        $result->closeCursor();

        if (sizeof($list) == 0)
            return new ModelCollection();

        if ($this->selectValues == null)
        {
            foreach ($list as $model)
                $model->hydrateBaseModel();

            return new ModelCollection($list);
        }

        return $list;
    }

    private function merge($pResults, $bResults, $keepPrimaryKey = false, $keepMetaId = false)
    {
        if ($bResults == null)
            return $pResults;

        if ($bResults instanceof ModelCollection)
        {
            if ($pResults == null)
            {
                $resultCollection = new ModelCollection();
                foreach ($bResults as $bResult)
                    $resultCollection->add($bResult->morphTo());

                return $resultCollection;
            }

            $resultCollection = new ModelCollection();
            foreach ($pResults as $pResult)
                $resultCollection->add($pResult);

            foreach ($bResults as $bResult)
            {
                foreach ($resultCollection as $model)
                {
                    if ($model->baseModel()->meta_id == $bResult->meta_id) continue;
                    $resultCollection->add($bResult->morphTo());
                }
            }

            return $resultCollection;
        }

        if ($pResults == null)
            return $bResults;

        $mergedArray = array();
        foreach ($pResults as $pResult)
        {
            foreach ($bResults as $bResult)
            {
                if ($pResult[$this->model->primaryKey()] == $bResult[Table::META_ID])
                {
                    $pTempArray = array();
                    foreach ($pResult as $field)
                    {
                        if (!$keepPrimaryKey && $field == $this->model->primaryKey()) continue;
                        $pTempArray[] = $field;
                    }

                    $bTempArray = array();
                    foreach ($bResult as $field)
                    {
                        if (!$keepMetaId && $field == Table::META_ID) continue;
                        $bTempArray[] = $field;
                    }

                    $mergedArray[] = array_merge($pTempArray, $bTempArray);
                    break;
                }
            }
        }

        return $mergedArray;
    }
}
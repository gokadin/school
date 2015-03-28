<?php namespace Library\Database;

use Library\Facades\DB;
use PDO;
use Symfony\Component\Yaml\Exception\RuntimeException;

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
        $this->polymorphicQueryLink = null;
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
        if ($values == null)
            return;

        if (is_array($values))
        {
            $this->selectValues = array_merge($this->selectValues, $values);
            return;
        }

        $this->selectValues[] = $values;
    }

    public function splitWheres()
    {
        $tempWheres = array();
        foreach ($this->wheres as $where)
        {
            if ($this->model->hasColumn($where['var']))
            {
                $tempWheres[] = $where;
                continue;
            }

            if ($this->model->baseModel()->hasColumn($where['var']))
                $this->baseWheres[] = $where;
        }

        $this->wheres = $tempWheres;

        if (sizeof($this->baseWheres) > 0)
            $this->polymorphicQueryLink = $this->baseWheres[0]['link'];
    }

    public function splitValues($values)
    {
        if ($values == null)
            return;

        if (!is_array($values))
        {
            if ($this->model->hasColumn($values))
                $this->selectValues[] = $values;
            else if ($this->model->baseModel()->hasColumn($values))
                $this->baseSelectValues[] = $values;

            return;
        }

        foreach ($values as $value)
        {
            if ($this->model->hasColumn($value))
                $this->selectValues[] = $value;
            else if ($this->model->baseModel()->hasColumn($value))
                $this->baseSelectValues[] = $value;
        }
    }

    public function get($values = null)
    {
        if (!$this->model->hasBaseModel() || sizeof($this->wheres) == 0)
        {
            $this->addValues($values);
            return $this->select();
        }

        if ($values != null)
            $this->doPolymorphicJoin($values);

        $this->splitWheres();

        $pResults = null;
        if (sizeof($this->wheres) > 0 && sizeof($this->baseWheres) == 0)
            return $this->select();

        $this->baseWheres[] = [
            'var' => Table::META_TYPE,
            'operator' => '=',
            'value' => $this->model->modelName(),
            'link' => 'AND'
        ];

        $baseQuery = $this->model->baseModel()->where($this->baseWheres[0]['var'],
            $this->baseWheres[0]['operator'],
            $this->baseWheres[0]['value'],
            $this->baseWheres[0]['link']);
        for ($i = 1; $i < sizeof($this->baseWheres); $i++)
            $baseQuery->where($this->baseWheres[$i]['var'],
                $this->baseWheres[$i]['operator'],
                $this->baseWheres[$i]['value'],
                $this->baseWheres[$i]['link']);

        $bResults = $baseQuery->get($this->baseSelectValues);

        if (sizeof($this->baseWheres) > 0 && sizeof($this->wheres) == 0)
        {
            $collection = new ModelCollection();
            foreach ($bResults as $bResult)
                $collection->add($bResult->morphTo());

            return $collection;
        }

        $pResults = $this->select();
        return $this->mergeModels($pResults, $bResults);
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
        {
            foreach ($list as $model)
                $model->hydrateBaseModel();

            return new ModelCollection($list);
        }

        return $list;
    }

    private function mergeModels($pResults, $bResults)
    {
        if ($pResults->count() == 0)
            return $bResults;

        if ($bResults->count() == 0)
            return $pResults;

        if (strtoupper($this->polymorphicQueryLink) == 'AND')
        {
            $metaIdField = Table::META_ID;
            $resultCollection = new ModelCollection();
            foreach ($pResults as $pModel)
            {
                foreach ($bResults as $bModel)
                {
                    if ($pModel->getPrimaryKey() == $bModel->$metaIdField)
                        $resultCollection->add($pModel);
                }
            }

            return $resultCollection;
        }

        if (strtoupper($this->polymorphicQueryLink) == 'OR')
        {
            $metaIdField = Table::META_ID;
            $resultCollection = new ModelCollection();
            foreach ($pResults as $pModel)
            {
                $resultCollection->add($pModel);
            }

            foreach ($bResults as $bModel)
            {
                if (!$resultCollection->hasPrimaryKey($bModel->$metaIdField))
                    $resultCollection->add($bModel->morphTo());
            }

            return $resultCollection;
        }

        return new ModelCollection();
    }

    private function doPolymorphicJoin($values)
    {
        throw new RuntimeException('Polymorphic selects with custom values are not supported at this time.');
    }
}
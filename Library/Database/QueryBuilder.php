<?php namespace Library\Database;

use Carbon\Carbon;

class QueryBuilder
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dao;
    protected $model;

    public function __construct($dao, $model)
    {
        $this->dao = $dao;
        $this->model = $model;
    }

    protected function buildSelect($selectValues, $wheres)
    {
        if ($selectValues == null || ($selectValues) == 0)
            $selectValues = '*';

        $str = 'SELECT ';

        if (is_array($selectValues))
        {
            $str .= implode(', ', $selectValues);
        }
        else
            $str .= $selectValues;

        $str .= ' FROM '.$this->model->tableName();

        if ($wheres == null || sizeof($wheres) == 0)
            return $str;

        $str .= $this->buildWhere($wheres);

        return $str;
    }

    protected function buildInsert($values)
    {
        $createdAtIsSet = false;
        $updatedAtIsSet = false;

        $str = 'INSERT INTO '.$this->model->tableName();

        $vars = array_keys($values);
        if ($this->model->hasTimestamps())
        {
            foreach ($vars as $var)
            {
                if ($var == self::CREATED_AT)
                    $createdAtIsSet = true;
                if ($var == self::UPDATED_AT)
                    $updatedAtIsSet = true;
            }

            if (!$createdAtIsSet)
                $vars[] = self::CREATED_AT;
            if (!$updatedAtIsSet)
                $vars[] = self::UPDATED_AT;
        }

        $str .= ' ('.implode(', ', $vars).')';
        $str .= ' VALUES(';

        $str .= implode(', ', $values);
        if (!$createdAtIsSet)
            $str .= ', '.Carbon::now();
        if (!$updatedAtIsSet)
            $str .= ', '.Carbon::now();

        $str .= ')';

        return $str;
    }

    protected function buildUpdate($values)
    {
        $str = 'UPDATE '.$this->model->tableName().' SET';
        $toImplode = array();
        foreach ($values as $key => $value)
        {
            $toImplode[] = $key.'='.$value;
        }
        $str .= implode(', ', $toImplode);

        return $str;
    }

    protected function buildDelete()
    {
        return 'DELETE FROM '.$this->model->tableName();
    }

    protected function buildWhere($wheres)
    {
        $str = ' WHERE '.$wheres[0]['var'].$wheres[0]['operator'].$wheres[0]['value'];

        for ($i = 1; $i < sizeof($wheres); $i++)
            $str .= ' '.$wheres[$i]['link'].' '.$wheres[$i]['var'].$wheres[$i]['operator'].$wheres[$i]['value'];

        return $str;
    }
}
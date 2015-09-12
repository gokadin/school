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

    protected function buildSelect($selectValues)
    {
        if ($selectValues == null || sizeof($selectValues) == 0)
            $selectValues = '*';

        $str = 'SELECT ';

        if (is_array($selectValues))
        {
            $str .= implode(', ', $selectValues);
        }
        else
            $str .= $selectValues;

        $str .= ' FROM '.$this->model->tableName();

        return $str;
    }

    protected function buildInsert($values = array())
    {
        $createdAtIsSet = false;
        $updatedAtIsSet = false;

        $str = 'INSERT INTO '.$this->model->tableName();

        $vars = array();
        if (sizeof($values) > 0)
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

        if ($this->model->hasTimestamps())
        {
            if (!$createdAtIsSet)
                $values[] = Carbon::now();
            if (!$updatedAtIsSet)
                $values[] = Carbon::now();
        }

        foreach ($values as &$value)
            $value = $this->addQuotes($value);

        $str .= implode(', ', $values);

        $str .= ')';

        return $str;
    }

    protected function buildUpdate($values)
    {
        $str = 'UPDATE '.$this->model->tableName().' SET ';
        $toImplode = array();
        foreach ($values as $key => $value)
        {
            $toImplode[] = $key.'='.$this->addQuotes($value);
        }
        $str .= implode(', ', $toImplode);

        return $str;
    }

    protected function buildDelete()
    {
        return 'DELETE FROM '.$this->model->tableName();
    }

    protected function buildWheres($wheres)
    {
        $str = ' WHERE '.$wheres[0]['var'].' '.$wheres[0]['operator'].' '.$wheres[0]['value'];

        for ($i = 1; $i < sizeof($wheres); $i++)
            $str .= ' '.$wheres[$i]['link'].' '.$wheres[$i]['var'].' '.$wheres[$i]['operator'].' '.$wheres[$i]['value'];

        return $str;
    }

    protected function buildJoins($joins)
    {
        $str = '';
        for ($i = 0; $i < sizeof($joins); $i++)
        {
            $str = ' JOIN ' . $joins[$i]['joinTableName'] . ' ON ';

            if ($joins[$i]['on'] != null)
                $str .= $joins[$i]['on'];
            else
                $str .= $this->model->tableName() . '.' . $this->model->primaryKeyName();

            $str .= ' ' . $joins[$i]['operator'] . ' ';

            if ($joins[$i]['to'] != null)
                $str .= $joins[$i]['to'];
            else
                $str .= $joins[$i]['joinTableName'] . '.' . $this->model->defaultForeignKey();
        }

        return $str;
    }

    protected function addQuotes($str)
    {
        return '\''.$str.'\'';
    }
}
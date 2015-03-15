<?php namespace Library\Database;

use Symfony\Component\Yaml\Exception\RuntimeException;
use IteratorAggregate;
use ArrayIterator;

class ModelCollection implements IteratorAggregate
{
    protected $models;

    public function __construct($models = array())
    {
        $this->models = $models;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->models);
    }

    public function add($model)
    {
        $this->models[] = $model;
    }

    public function first($default = null)
    {
        return $this->count() > 0 ? reset($this->models) : $default;
    }

    public function last($default = null)
    {
        return $this->count() > 0 ? end($this->models) : $default;
    }

    public function count()
    {
        return count($this->models);
    }

    public function isEmpty()
    {
        return empty($this->models);
    }

    public function where($var, $operator, $value)
    {
        if ($this->count() == 0)
            return null;

        if (!$this->models[0]->hasColumn($var))
        {
            throw new RuntimeException('Model '.$this->models[0]->modelName().' has no field named '.$var);
            return null;
        }

        $operator = trim($operator);
        $results = new ModelCollection();
        foreach ($this->models as $model)
        {
            switch ($operator)
            {
                case '=':
                    if ($model->$var === $value)
                        $results->add($model);
                    break;
                case '>':
                    if ($model->$var > $value)
                        $results->add($model);
                    break;
                case '<':
                    if ($model->$var < $value)
                        $results->add($model);
                    break;
                case '>=':
                    if ($model->$var >= $value)
                        $results->add($model);
                    break;
                case '<=':
                    if ($model->$var <= $value)
                        $results->add($model);
                    break;
                default:
                    throw new RuntimeException('Operator '.$operator.' is invalid.');
                    return $this;
            }
        }

        return $results;
    }

    public function groupBy($field)
    {
        if ($this->count() == 0)
            return null;

        if (!$this->models[0]->hasColumn($field))
        {
            throw new RuntimeException('Model '.$this->models[0]->modelName().' has no field named '.$field);
            return null;
        }

        $results = array();
        foreach ($this->models as $model)
        {
            if (!isset($results[$model->$field]))
                $results[$model->$field] = new ModelCollection();

           $results[$model->$field]->add($model);
        }

        return $results;
    }

    public function sortBy($field, $descending = false)
    {
        if ($this->count() == 0)
            return null;

        if (!$this->models[0]->hasColumn($field))
        {
            throw new RuntimeException('Model '.$this->models[0]->modelName().' has no field named '.$field);
            return null;
        }

        if (!$descending)
        {
            usort($this->models, function($a, $b)
            {
                return $a->$field > $b->$field;
            });
        }
        else
        {
            usort($this->models, function($a, $b)
            {
                return $a->$field < $b->$field;
            });
        }

        return $this;
    }
}
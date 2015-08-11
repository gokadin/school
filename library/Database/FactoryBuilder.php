<?php

namespace Library\Database;

class FactoryBuilder
{
    protected $class;
    protected $definitions;
    protected $faker;

    public function __construct($class, $definitions, $faker)
    {
        $this->class = $class;
        $this->definitions = $definitions;
        $this->faker = $faker;
    }

    public function make($count = 1, array $attributes = [])
    {
        if (!isset($count) || !is_int($count) || $count < 1)
        {
            $count = 1;
        }

        $modelCollection = new ModelCollection();
        for ($i = 0; $i < $count; $i++)
        {
            $properties = $this->overwriteAttributes($this->definitions[$this->class]($this->faker), $attributes);
            $modelCollection->add($this->generateModel($properties));
        }

        if ($count == 1)
        {
            return $modelCollection->first();
        }

        return $modelCollection;
    }

    public function create($count = 1, array $attributes = [])
    {
        if (!isset($count) || !is_int($count) || $count < 1)
        {
            $count = 1;
        }

        $modelCollection = new ModelCollection();
        for ($i = 0; $i < $count; $i++)
        {
            $properties = $this->overwriteAttributes($this->definitions[$this->class]($this->faker), $attributes);
            $model = $this->generateModel($properties);
            $model->save();
            $modelCollection->add($model);
        }

        if ($count == 1)
        {
            return $modelCollection->first();
        }

        return $modelCollection;
    }

    protected function overwriteAttributes($properties, $attributes)
    {
        foreach ($attributes as $key => $value)
        {
            if (isset($properties[$key]))
            {
                $properties[$key] = $value;
            }
        }

        return $properties;
    }

    protected function generateModel($properties)
    {
        $model = new $this->class();

        foreach ($properties as $key => $value)
        {
            $model->$key = $value;
        }

        return $model;
    }
}
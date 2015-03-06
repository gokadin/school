<?php namespace Library\Database;

use Database\Tables;
use Symfony\Component\Yaml\Exception\RuntimeException;

class TableBuilder extends Tables
{
    protected $blueprints;

    public function __construct()
    {
        $functions = get_class_methods('Database\Tables');

        foreach ($functions as $function)
            $this->blueprints[] = $this->$function();
    }

    public function getBlueprint($modelName)
    {
        foreach ($this->blueprints as $blueprint)
        {
            if ($blueprint->modelName() == $modelName)
                return $blueprint;
        }

        throw new RuntimeException('Table schema for '.$modelName.' does not exist.');
    }
}
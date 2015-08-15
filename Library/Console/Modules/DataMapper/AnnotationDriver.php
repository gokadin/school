<?php

namespace Library\Console\Modules\DataMapper;

use ReflectionClass;

class AnnotationDriver
{
    protected $classes;

    public function __construct($classes)
    {
        $this->classes = $classes;
    }

    public function build()
    {
        $result = [];

        if (sizeof($this->classes) == 0)
        {
            return $result;
        }

        foreach ($this->classes as $class)
        {
            $result[$class] = $this->buildClass($class);
        }

        return $result;
    }

    protected function buildClass($class)
    {
        $r = new ReflectionClass($class);

        return [
            'name' => $this->getName($class),
            'columns' => $this->buildColumns($r),
            'relationships' => $this->buildRelationships($r)
        ];
    }

    protected function getName($class)
    {
        $pos = strrpos($class, '\\');
        return $pos !== false ? substr($class, $pos + 1) : $class;
    }

    protected function buildColumns($r)
    {
        return [];
    }

    protected function buildRelationships($r)
    {
        return [];
    }
}
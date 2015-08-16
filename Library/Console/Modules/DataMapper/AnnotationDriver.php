<?php

namespace Library\Console\Modules\DataMapper;

use Library\Database\Database;
use Library\Database\Schema;
use Library\Database\Table;
use ReflectionClass;
use ReflectionProperty;

class AnnotationDriver
{
    const ANNOTATION_REGEX = '/@(\w+)(?:\(([^)]+)+\))?/';

    protected $database;
    protected $classes;

    public function __construct(Database $database, $classes)
    {
        $this->database = $database;
        $this->classes = $classes;
    }

    public function build()
    {
        $schema = new Schema($this->database);

        if (sizeof($this->classes) == 0)
        {
            return $schema;
        }

        foreach ($this->classes as $class)
        {
            $schema->add($this->buildClass($class));
        }

        return $schema;
    }

    protected function buildClass($class)
    {
        $r = new ReflectionClass($class);

        $t = new Table($this->getName($r));

        $this->buildColumns($r, $t);

        return $t;
    }

    protected function getName(ReflectionClass $r)
    {
        $parsed = $this->parseDocComment($r->getDocComment());
        if (sizeof($parsed) == 0 || !isset($parsed['Entity']))
        {
            return $r->getShortName();
        }

        return $parsed['Entity']['name'];
    }

    protected function buildColumns(ReflectionClass $r, Table &$table)
    {
        $properties = $r->getProperties();

        foreach ($properties as $property)
        {
            $parsed = $this->parseDocComment($property->getDocComment());

            if (sizeof($parsed) == 0 ||
                (!isset($parsed['Column']) && !isset($parsed['Id'])) ||
                (isset($parsed['Column']) && !isset($parsed['Column']['type'])))
            {
                continue;
            }

            if (isset($parsed['Id']))
            {
                $table->increments($this->getColumnName($property, $parsed['Id']));
                continue;
            }

            $columnArgs = $parsed['Column'];
            $columnName = $this->getColumnName($property, $columnArgs);
            $columnType = strtolower($parsed['Column']['type']);
            switch ($columnType)
            {
                case 'integer':
                    isset($parsed['Column']['size'])
                        ? $table->integer($columnName, $columnArgs['size'])
                        : $table->integer($columnName);
                    break;
                case 'string':
                    isset($columnArgs['size'])
                        ? $table->string($columnName, $columnArgs['size'])
                        : $table->string($columnName);
                    break;
                case 'decimal':
                    isset($columnArgs['size'])
                        ? (isset($columnArgs['precision'])
                            ? $table->decimal($columnName, $columnArgs['size'], $columnArgs['precision'])
                            : $table->decimal($columnName, $columnArgs['size']))
                        : $table->decimal($columnName);
                    break;
                case 'text':
                    $table->text($columnName);
                    break;
                case 'boolean':
                    $table->boolean($columnName);
                    break;
                case 'datetime':
                    $table->datetime($columnName);
                    break;
                default:
                    continue;
                    break;
            }
        }
    }

    protected function buildRelationships(ReflectionClass $r)
    {
        return [];
    }

    protected function getColumnName(ReflectionProperty $property, $parsed)
    {
        if (isset($parsed['name']))
        {
            return $parsed['name'];
        }

        return $property->getName();
    }

    protected function parseDocComment($doc)
    {
        $parsed = [];
        preg_match_all(self::ANNOTATION_REGEX, $doc, $matches);
        if (sizeof($matches[1]) == 0)
        {
            return $parsed;
        }

        for ($i = 0; $i < sizeof($matches[1]); $i++)
        {
            $parsed[$matches[1][$i]] = $this->parseArguments($matches[2][$i]);
        }

        return $parsed;
    }

    protected function parseArguments($args)
    {
        $args = explode(',', $args);

        $parsed = [];

        foreach ($args as $arg)
        {
            $arg = trim($arg);
            $name = substr($arg, 0, strpos($arg, '='));
            $value = substr(substr($arg, strpos($arg, '"') + 1), 0, -1);
            if (!$value)
            {
                $value = [];
            }

            $parsed[$name] = $value;
        }

        return $parsed;
    }
}
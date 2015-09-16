<?php

namespace Library\DataMapper\Mapping\Drivers;

use Library\DataMapper\Mapping\Column;
use Library\DataMapper\Mapping\Metadata;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Yaml\Exception\RuntimeException;

class AnnotationDriver
{
    const ANNOTATION_REGEX = '/@(\w+)(?:\(([^)]+)+\))?/';
    const DEFAULT_COLUMN_SIZE = 11;

    public function getMetadata($class)
    {
        $r = new ReflectionClass($class);

        $parsed = $this->parseDocComment($r->getDocComment());
        if (sizeof($parsed) == 0 || !isset($parsed['Entity']))
        {
            throw new RuntimeException('Class '.$class.' not parsable.');
        }

        $metadata = new Metadata($parsed['Entity']['name'], $r);

        $properties = $r->getProperties();
        foreach ($properties as $property)
        {
            $column = $this->buildColumn($property);
            if (is_null($column))
            {
                continue;
            }

            $metadata->addColumn($column);
        }

        return $metadata;
    }

    protected function buildColumn(ReflectionProperty $property)
    {
        $parsed = $this->parseDocComment($property->getDocComment());
        if (sizeof($parsed) == 0 ||
            (!isset($parsed['Column']) && !isset($parsed['Id'])) ||
            (isset($parsed['Column']) && !isset($parsed['Column']['type'])))
        {
            return null;
        }

        $fieldName = $property->getName();
        $columnName = isset($parsed['Column']['name'])
            ? $parsed['Column']['name']
            : $fieldName;
        $size = isset($parsed['Column']['size'])
            ? $parsed['Column']['size']
            : self::DEFAULT_COLUMN_SIZE;
        $type = isset($parsed['Column']['type'])
            ? $parsed['Column']['type']
            : 'integer';

        $column = new Column($columnName, $fieldName, $type, $size);

        if (isset($parsed['Id']))
        {
            $column->setPrimaryKey();
            return $column;
        }

        $columnArgs = $parsed['Column'];

        if ($type == 'decimal' && isset($columnArgs['precision']))
        {
            $column->setPrecision($columnArgs['precision']);
        }

        if (isset($columnArgs['indexed']) && $columnArgs['indexed'])
        {
            $column->setIndex();
        }

        if (isset($columnArgs['nullable']) && $columnArgs['nullable'])
        {
            $column->setNullable();
        }

        if (isset($columnArgs['default']))
        {
            $column->setDefaultValue($columnArgs['default']);
        }

        return $column;
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
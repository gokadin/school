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
    const DEFAULT_INTEGER_SIZE = 11;
    const DEFAULT_STRING_SIZE = 255;
    const DEFAULT_DECIMAL_PRECISION = 2;
    const TEXT_SIZE = 65535;
    const BOOLEAN_SIZE = 1;

    public function getMetadata($class)
    {
        $r = new ReflectionClass($class);

        $parsed = $this->parseDocComment($r->getDocComment());
        if (sizeof($parsed) == 0 || !isset($parsed['Entity']))
        {
            throw new RuntimeException('Class '.$class.' not parsable.');
        }

        $table = isset($parsed['Entity']['name'])
            ? $parsed['Entity']['name']
            : $r->getShortName();
        $metadata = new Metadata($table, $r);

        $properties = $r->getProperties();
        foreach ($properties as $property)
        {
            $parsedProperty = $this->parseDocComment($property->getDocComment());

            if (isset($parsedProperty['Column']) || isset($parsedProperty['Id']))
            {
                $column = $this->buildColumn($property, $parsedProperty);
                if (is_null($column))
                {
                    continue;
                }

                $metadata->addColumn($column);
            }
            else if (isset($parsedProperty[Metadata::ASSOC_HAS_MANY]))
            {
                $metadata->addAssociation([
                    'type' => Metadata::ASSOC_HAS_MANY,
                    'target' => $parsedProperty[Metadata::ASSOC_HAS_MANY]['target'],
                    'propName' => $property->getName(),
                    'mappedBy' => $parsedProperty[Metadata::ASSOC_HAS_MANY]['mappedBy']
                ]);
            }
            else if (isset($parsedProperty[Metadata::ASSOC_HAS_ONE]))
            {
                $target = $parsedProperty[Metadata::ASSOC_HAS_ONE]['target'];
                $targetShortName = substr($target, strrpos($target, '\\') + 1);

                $metadata->addAssociation([
                    'type' => Metadata::ASSOC_HAS_ONE,
                    'target' => $target,
                    'propName' => $property->getName()
                ]);

                $column = new Column(
                    lcfirst($targetShortName).'_id',
                    $property->getName(),
                    'integer',
                    self::DEFAULT_INTEGER_SIZE
                );
                $column->setForeignKey();
                if (isset($parsedProperty[Metadata::ASSOC_HAS_ONE]['nullable'])
                    && $parsedProperty[Metadata::ASSOC_HAS_ONE]['nullable'])
                {
                    $column->setNullable();
                }

                $metadata->addColumn($column);
            }
            else if (isset($parsedProperty[Metadata::ASSOC_BELONGS_TO]))
            {
                $target = $parsedProperty[Metadata::ASSOC_BELONGS_TO]['target'];
                $targetShortName = substr($target, strrpos($target, '\\') + 1);

                $metadata->addAssociation([
                    'type' => Metadata::ASSOC_BELONGS_TO,
                    'target' => $target,
                    'propName' => $property->getName()
                ]);

                $column = new Column(
                    lcfirst($targetShortName).'_id',
                    $property->getName(),
                    'integer',
                    self::DEFAULT_INTEGER_SIZE
                );
                $column->setForeignKey();
                if (isset($parsedProperty[Metadata::ASSOC_BELONGS_TO]['nullable'])
                    && $parsedProperty[Metadata::ASSOC_BELONGS_TO]['nullable'])
                {
                    $column->setNullable();
                }

                $metadata->addColumn($column);
            }
        }

        return $metadata;
    }

    protected function buildColumn(ReflectionProperty $property, $parsed)
    {
        if (sizeof($parsed) == 0 ||
            (!isset($parsed['Column']) && !isset($parsed['Id'])) ||
            (isset($parsed['Column']) && !isset($parsed['Column']['type'])))
        {
            return null;
        }

        $propName = $property->getName();
        $columnName = isset($parsed['Column']['name'])
            ? $parsed['Column']['name']
            : $propName;
        $type = isset($parsed['Column']['type'])
            ? $parsed['Column']['type']
            : 'integer';

        $size = 0;
        switch ($type)
        {
            case 'text':
                $size = self::TEXT_SIZE;
                break;
            case 'boolean':
                $size = self::BOOLEAN_SIZE;
                break;
            case 'string':
                $size = isset($parsed['Column']['size'])
                    ? $parsed['Column']['size']
                    : self::DEFAULT_STRING_SIZE;
                break;
            case 'integer':
            case 'decimal':
                $size = isset($parsed['Column']['size'])
                    ? $parsed['Column']['size']
                    : self::DEFAULT_INTEGER_SIZE;
                break;
        }

        $column = new Column($columnName, $propName, $type, $size);

        if (isset($parsed['Id']))
        {
            $column->setPrimaryKey();
            return $column;
        }

        $columnArgs = $parsed['Column'];

        if ($type == 'decimal')
        {
            $column->setPrecision(isset($columnArgs['precision'])
                ? $columnArgs['precision']
                : self::DEFAULT_DECIMAL_PRECISION);
        }

        if (isset($columnArgs['indexed']))
        {
            $column->setIndex();
        }

        if (isset($columnArgs['nullable']))
        {
            $column->setNullable();
        }

        if (isset($columnArgs['default']))
        {
            $column->setDefaultValue($columnArgs['default']);
        }

        if (isset($columnArgs['unique']))
        {
            $column->unique();
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
            $segments = explode('=', $arg);
            $name = $segments[0];
            $value = [];
            if (sizeof($segments) == 2)
            {
                $value = trim($segments[1], '"');
            }

            $parsed[$name] = $value;
        }

        return $parsed;
    }
}
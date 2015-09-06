<?php

namespace Library\Database\DataMapper;

use Carbon\Carbon;
use Library\Console\Modules\DataMapper\DataMapperRedisCacheDriver;
use Library\Database\Column;
use Library\Database\Database;
use Library\Database\Table;
use Symfony\Component\Yaml\Exception\RuntimeException;
use ReflectionClass;
use ReflectionException;

class DataMapper
{
    protected $entities = [];
    protected $database;
    protected $cacheDriver;
    protected $classes;
    protected $commands = [];

    public function __construct(Database $database, $settings)
    {
        $this->database = $database;
        $this->classes = $settings['classes'];

        $this->initializeCacheDriver($settings);
    }

    protected function initializeCacheDriver($settings)
    {
        switch ($settings['config']['cacheDriver'])
        {
            default:
                $this->cacheDriver = new DataMapperRedisCacheDriver($settings['config']['redisDatabase']);
                break;
        }
    }

    public function persist($object)
    {
        if (is_null($object))
        {
            return;
        }

        $this->addCommand('persist', $object);
    }

    public function find($class, $id)
    {
        if (!isset($this->classes[$class]))
        {
            return null;
        }

        $table = $this->cacheDriver->getTableByClass($class);
        $data = $this->database->table($table->name())
            ->where($table->getPrimaryKey()->getName(), $id)
            ->get();

        if (sizeof($data) == 0)
        {
            return null;
        }

        return $this->buildEntity($table, $data);
    }

    public function findOrFail($class, $id)
    {
        $entity = $this->find($class, $id);
        if (is_null($entity))
        {
            throw new RuntimeException('Could not find entity '.$class.' with id '.$id);
        }

        return $entity;
    }

    public function flush()
    {
        foreach ($this->commands as $name => $data)
        {
            $commandName = 'execute'.ucfirst($name);
            is_null($data) ? $this->$commandName() : $this->$commandName($data);
        }
    }

    protected function addCommand($name, $data = null)
    {
        $this->commands[$name] = $data;
    }

    protected function buildEntity(Table $table, $data)
    {
        $entityName = $table->modelName();
        $entity = new $entityName();
        $r = new ReflectionClass($entity);

        $properties = $r->getProperties();

        $columnPropertyNames = array_keys($table->columns());
        foreach ($properties as $property)
        {
            if (!in_array($property->getName(), $columnPropertyNames))
            {
                continue;
            }

            $r->getProperty($property->getName())->setValue($data[$table->columns()[$property->getName()]->getName()]);
        }

        return $entity;
    }

    protected function executePersist($object)
    {
        $table = $this->cacheDriver->getTableByClass(get_class($object));

        if (is_null($table))
        {
            throw new RuntimeException('Could not persist entity '.get_class($object).'. Entity record not found.');
        }

        $r = new ReflectionClass($object);
        $primaryKey = $r->getProperty($table->getPrimaryKey()->getPropertyName());
        $primaryKey->setAccessible(true);
        if (is_null($primaryKey->getValue($object)))
        {
            $this->insert($object, $table, $r);
        }

        $this->update($object, $table);
    }

    protected function insert($object, $table, ReflectionClass $r)
    {
        $data = [];
        foreach ($table->columns() as $column)
        {
            try
            {
                $property = $r->getProperty($column->getPropertyName());
                $property->setAccessible(true);
            }
            catch (ReflectionException $e)
            {
                continue;
            }

            $value = $property->getValue($object);
            if (is_null($value))
            {
                if ($column->getName() == Column::CREATED_AT || $column->getName() == Column::UPDATED_AT)
                {
                    $data[$column->getName()] = Carbon::now();
                    continue;
                }
            }

            $data[$column->getName()] = $value;
        }

        $this->database->table($table->name())->insert($data);

        $this->entities[$r->getName()][$data[$table->getPrimaryKey()->getPropertyName()]] = $object;
    }

    protected function update($object, $table)
    {

    }
}
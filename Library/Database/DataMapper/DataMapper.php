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

        $this->addCommand('persist', get_class($object), [$object]);
    }

    public function find($class, $id)
    {
        if (!in_array($class, $this->classes))
        {
            return null;
        }

        $table = $this->cacheDriver->getTableByClass($class);
        $data = $this->database->table($table->name())
            ->where($table->getPrimaryKey()->getName(), $id)
            ->select();

        if (sizeof($data) == 0)
        {
            return null;
        }

        return $this->buildEntity($class, $table, $data[0]);
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

    public function delete($classOrObject, $id = 0)
    {
        if (is_object($classOrObject))
        {
            $this->addCommand('delete', get_class($classOrObject), [$classOrObject]);
            return;
        }

        $this->addCommand('delete', $classOrObject, [$classOrObject, $id]);
    }

    public function flush()
    {
        foreach ($this->commands as $command => $classes)
        {
            foreach ($classes as $class => $data)
            {
                foreach ($data as $args)
                {
                    switch ($command)
                    {
                        case 'persist':
                            $this->executePersist($args[0]);
                            break;
                        case 'delete':
                            sizeof($args) == 1
                                ? $this->executeDeleteOnObject($args[0])
                                : $this->executeDeleteOnClass($args[0], $args[1]);
                            break;
                    }
                }
            }
        }
    }

    protected function addCommand($name, $class, array $args = [])
    {
        $this->commands[$name][$class][] = $args;
    }

    protected function buildEntity($class, Table $table, $data)
    {
        $r = new ReflectionClass($class);
        $entity = $r->newInstanceWithoutConstructor();

        foreach ($table->columns() as $column)
        {
            $property = $r->getProperty($column->getPropertyName());
            $property->setAccessible(true);

            if (!array_key_exists($column->getName(), $data))
            {
                throw new RuntimeException('Could not build entity '.$class
                    .'. Column '.$column->getName().' was not found in the database.');
            }

            $property->setValue($entity, $data[$column->getName()]);
        }

        return $entity;
    }

    protected function executePersist($object)
    {
        $class = get_class($object);
        $table = $this->cacheDriver->getTableByClass($class);

        if (is_null($table))
        {
            throw new RuntimeException('Could not persist entity '.$class.'. Entity record not found.');
        }

        $r = new ReflectionClass($object);
        $primaryKey = $r->getProperty($table->getPrimaryKey()->getPropertyName());
        $primaryKey->setAccessible(true);
        if (is_null($primaryKey->getValue($object)))
        {
            return $this->insert($object, $table, $r);
        }

        return $this->update($object, $table, $r);
    }

    protected function insert($object, $table, ReflectionClass $r)
    {
        $data = [];
        foreach ($table->columns() as $column)
        {
            if ($column->isPrimaryKey())
            {
                continue;
            }

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

        $id = $this->database->table($table->name())->insert($data);

        $primaryKey = $r->getProperty($table->getPrimaryKey()->getPropertyName());
        $primaryKey->setAccessible(true);
        $primaryKey->setValue($object, $id);
    }

    protected function update($object, $table, ReflectionClass $r)
    {
        $id = 0;
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

            if ($column->getName() == Column::UPDATED_AT)
            {
                $data[$column->getName()] = Carbon::now();
                continue;
            }

            if ($column->isPrimaryKey())
            {
                $id = $property->getValue($object);
                continue;
            }

            $value = $property->getValue($object);

            $data[$column->getName()] = $value;
        }

        if ($id == 0)
        {
            return false;
        }

        $this->database->table($table->name())
            ->where($table->getPrimaryKey()->getName(), $id)
            ->update($data);
    }

    protected function executeDeleteOnObject($object)
    {
        $r = new ReflectionClass($object);

        $table = $this->cacheDriver->getTableByClass(get_class($object));

        $primaryKeyColumn = $table->getPrimaryKey();
        $primaryKey = $r->getProperty($primaryKeyColumn->getPropertyName());
        $primaryKey->setAccessible(true);
        $id = $primaryKey->getValue($object);

        $this->database->table($table->name())
            ->where($primaryKeyColumn->getName(), $id)
            ->delete();
    }

    protected function executeDeleteOnClass($class, $id)
    {
        $table = $this->cacheDriver->getTableByClass($class);

        $this->database->table($table->name())
            ->where($table->getPrimarykey()->getName(), $id)
            ->delete();
    }
}
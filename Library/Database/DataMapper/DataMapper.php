<?php

namespace Library\Database\DataMapper;

use Library\Console\Modules\DataMapper\RedisCacheDriver;
use Library\Database\Database;
use Symfony\Component\Yaml\Exception\RuntimeException;

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

        $this->initializeCacheDriver($settings['config']['cacheDriver']);
    }

    protected function initializeCacheDriver($cacheDriver)
    {
        switch ($cacheDriver)
        {
            default:
                $this->cacheDriver = new RedisCacheDriver();
                break;
        }
    }

    public function persist($object)
    {
        $this->addCommand('persist', $object);
    }

    public function find($class, $id)
    {
        if (!isset($this->classes[$class]))
        {
            return null;
        }

        $tableName = $this->cacheDriver->getTableByClass($class);
        return $this->buildEntity($this->database->select($tableName));
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
            if (is_null($data))
            {
                $this->database->$name();
                continue;
            }

            $this->database->$name($data);
        }
    }

    protected function addCommand($name, $data = null)
    {
        $this->commands[$name] = $data;
    }

    protected function buildEntity($data)
    {
        return $data;
    }
}
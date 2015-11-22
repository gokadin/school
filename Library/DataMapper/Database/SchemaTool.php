<?php

namespace Library\DataMapper\Database;

use Library\DataMapper\Database\Drivers\MySqlDriver;
use Library\DataMapper\Mapping\Drivers\AnnotationDriver;

class SchemaTool
{
    protected $mappingDriver;
    protected $databaseDriver;
    protected $classes;

    public function __construct($config)
    {
        $this->classes = $config['classes'];

        $this->initializeMappingDriver($config['mappingDriver']);
        $this->initializeDatabaseDriver($config);
    }

    protected function initializeDatabaseDriver($config)
    {
        switch ($config['databaseDriver'])
        {
            default:
                $this->databaseDriver = new MySqlDriver($config[$config['databaseDriver']]);
                break;
        }
    }

    protected function initializeMappingDriver($driverName)
    {
        switch ($driverName)
        {
            default:
                $this->mappingDriver = new AnnotationDriver();
                break;
        }
    }

    public function create()
    {
        $results = [];

        foreach ($this->classes as $class)
        {
            $metadata = $this->mappingDriver->getMetadata($class);
            if ($this->databaseDriver->createTable($metadata))
            {
                $results[$metadata->table()] = true;
                continue;
            }

            $results[$metadata->table()] = false;
        }

        return $results;
    }

    public function drop()
    {
        foreach ($this->classes as $class)
        {
            $metadata = $this->mappingDriver->getMetadata($class);
            $this->databaseDriver->dropTable($metadata->table());
        }
    }

    public function update()
    {
        foreach ($this->classes as $class)
        {
            $metadata = $this->mappingDriver->getMetadata($class);
        }
    }
}
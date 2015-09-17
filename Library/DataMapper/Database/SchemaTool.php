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
        foreach ($this->classes as $class)
        {
            $metadata = $this->mappingDriver->getMetadata($class);
            $this->databaseDriver->createTable($metadata);
        }
    }

    public function drop()
    {
        foreach ($this->classes as $class)
        {
            $metadata = $this->mappingDriver->getMetadata($class);
            $this->databaseDriver->dropTable($metadata->table());
        }
    }
}
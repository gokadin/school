<?php

namespace Library\Console\Modules\DataMapper;

use Symfony\Component\Console\Command\Command;

class DataMapper extends Command
{
    protected $mappingDriver;
    protected $cacheDriver;

    public function __construct()
    {
        $settings = require App::basePath().'Config/datamapper.php';

        $this->initializeDrivers($settings);
    }

    protected function initializeDrivers($settings)
    {
        $this->initializeMappingDriver($settings);
        $this->initializeCacheDriver($settings);
    }

    protected function initializeMappingDriver($settings)
    {
        switch ($settings['config']['mappingDriver'])
        {
            default:
                $this->mappingDriver = new AnnotationDriver($settings['classes']);
                break;
        }
    }

    protected function initializeCacheDriver($settings)
    {
        switch ($settings['config']['cacheDriver'])
        {
            default:
                $this->cacheDriver = new RedisCacheDriver();
                break;
        }
    }

    protected function configure()
    {
        $this
            ->setName('datamapper:map')
            ->setDescription('Map entities to the storage driver.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // step 1: build mapping in memory
        $this->mappingDriver->build();

        // step 2: insert mapping into cache

        // step 3: create schema if needed
    }
}
<?php

namespace Library\Console\Modules\DataMapper;

use Library\Database\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DataMapper extends Command
{
    protected $database;
    protected $mappingDriver;
    protected $cacheDriver;

    public function __construct(Database $database, $settings)
    {
        parent::__construct();

        $this->database = $database;

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
                $this->mappingDriver = new AnnotationDriver($this->database, $settings['classes']);
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
        $output->writeln('<info>Creating schema...</info>');
        $schema = $this->mappingDriver->build();
        $schema->createAll();
        $output->writeln('<info>Schema created.</info>');

        $output->writeln('<info>Loading schema into cache...</info>');
        $this->cacheDriver->loadSchema($schema);
        $output->writeln('<info>Schema loaded.</info>');
    }
}
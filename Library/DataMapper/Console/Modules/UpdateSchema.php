<?php

namespace Library\DataMapper\Console\Modules;

use Library\DataMapper\Database\SchemaTool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateSchema extends Command
{
    protected $config;

    public function __construct($config)
    {
        parent::__construct();

        $this->config = $config;
    }

    protected function configure()
    {
        $this
            ->setName('schema:update')
            ->setDescription('Update schema.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $schemaTool = new SchemaTool($this->config);
        $results = $schemaTool->update();

        foreach ($results as $table => $result)
        {
            // ...
        }
    }
}
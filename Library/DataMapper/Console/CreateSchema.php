<?php

namespace Library\DataMapper\Console;

use Library\DataMapper\Database\SchemaTool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateSchema extends Command
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
            ->setName('schema:create')
            ->setDescription('Create schema.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $schemaTool = new SchemaTool($this->config);
        $schemaTool->create();

        $output->writeln('<info>Schema created.</info>');
    }
}
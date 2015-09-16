<?php

namespace Library\DataMapper\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateSchema extends Command
{
    protected $classes = [];

    public function __construct($config)
    {
        parent::__construct();

        $this->classes = $config['classes'];
    }

    protected function configure()
    {
        $this
            ->setName('schema:create')
            ->setDescription('Create schema.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Schema created.</info>');
    }
}
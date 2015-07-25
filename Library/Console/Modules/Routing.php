<?php

namespace Library\Console\Modules;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Routing extends Command
{
    protected function configure()
    {
        $this
            ->setName('test:test')
            ->setDescription('testing...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('executing test');
    }
}
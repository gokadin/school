<?php

namespace Library\Console\Modules\Queue;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QueueListener extends Command
{
    protected function configure()
    {
        $this
            ->setName('queue:listen')
            ->setDescription('Listen to a queue.')
            ->addArgument('name', InputArgument::OPTIONAL, 'The queue name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $dao = $this->getDbConnection();

        while (true)
        {
            // get next job

            // run job

            // write log if job was run

            // write log saying its alive

            // sleep
        }
    }

    protected function getDbConnection()
    {
        $settings = include __DIR__.'/../../../../Config/database.php';

        $dao = new \PDO($settings['mysql']['driver'].':host='.$settings['mysql']['host'].';dbname='.$settings['mysql']['database'],
            $settings['mysql']['username'],
            $settings['mysql']['password']);

        $dao->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $dao;
    }

    protected function getNextJob()
    {

    }
}
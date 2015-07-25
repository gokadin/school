<?php

namespace Library\Console\Modules\Queue;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QueueListener extends Command
{
    const SLEEP_DURATION_SEC = 2;

    protected $app;
    protected $dao;
    protected $queueTable;
    // protected $failedTable;

    public function __construct($app)
    {
        $this->app = $app;
        $this->dao = $this->getDbConnection();
        $this->queueTable = $this->getQueueTableName();
    }

    protected function configure()
    {
        $this
            ->setName('queue:listen')
            ->setDescription('Listen to a queue.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        while (true)
        {
            $job = $this->getNextJob();

            if (is_null($job))
            {
                sleep(self::SLEEP_DURATION_SEC);
            }

            $job->handle();

            // write log if job was run

            // write log saying its alive

            sleep(self::SLEEP_DURATION_SEC);
        }
    }

    protected function getDbConnection()
    {
        $settings = include $this->app->basePath().'Config/database.php';

        $dao = new \PDO($settings['mysql']['driver'].':host='.$settings['mysql']['host'].';dbname='.$settings['mysql']['database'],
            $settings['mysql']['username'],
            $settings['mysql']['password']);

        $dao->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $dao;
    }

    protected function getQueueTableName()
    {
        $settings = require $this->app->basePath().'Config/query.php';
        return $settings['connections']['database']['table'];
    }

    protected function getNextJob()
    {
        $query = $this->dao->prepare('SELECT FROM '.$this->queueTable.' '.
            'ORDER BY execution_date ASC LIMIT 1');

        $query->execute();

        $row = $query->fetch();
    }
}
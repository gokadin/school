<?php

namespace Library\Console\Modules\Queue;

use Library\Container\ContainerException;
use Library\Facades\App;
use Library\Facades\Log;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

class QueueListener extends Command
{
    const SLEEP_DURATION_SEC = 2;

    protected $dao;
    protected $queueTable;
    protected $failedTable;

    public function __construct()
    {
        parent::__construct();

        $this->dao = $this->getDbConnection();

        $settings = require App::basePath().'Config/queue.php';
        $this->queueTable = $settings['connections']['database']['table'];
        $this->failedTable = $settings['connections']['database']['failedTable'];
    }

    protected function configure()
    {
        $this
            ->setName('queue:listen')
            ->setDescription('Listen to a queue.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $attempts = 0;
        while (true)
        {
            list($id, $maxAttempts, $job) = $this->getNextJobArray();

            if (is_null($job))
            {
                $output->writeln('<comment>Waiting for new job...</comment>');
                sleep(self::SLEEP_DURATION_SEC);
                continue;
            }

            try
            {
                $parameters = App::container()->resolveMethodParameters($job, 'handle');
                call_user_func_array([$job, 'handle'], $parameters);
                $attempts = 0;
                $this->removeJob($id);
            }
            catch (Exception $e)
            {
                $attempts++;
                $output->writeln('<error>Failed processing job #'.$id.' : '.
                    get_class($job).' (attempt '.$attempts.').</error>');

                Log::error('Failed processing job #'.$id.' : '.
                    get_class($job).' (attempt '.$attempts.'), message: '.$e->getMessage());

                if ($attempts >= $maxAttempts)
                {
                    $this->handleFailedJob($job);
                    $this->removeJob($id);
                    $attempts = 0;
                }
            }

            $output->writeln('<info>Processed job #'.$id.' : '.get_class($job).'.</info>');
            sleep(self::SLEEP_DURATION_SEC);
        }
    }

    protected function getDbConnection()
    {
        $settings = include App::basePath().'Config/database.php';

        $dao = new \PDO($settings['mysql']['driver'].':host='.$settings['mysql']['host'].';dbname='.$settings['mysql']['database'],
            $settings['mysql']['username'],
            $settings['mysql']['password']);

        $dao->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $dao;
    }

    protected function getNextJobArray()
    {
        $query = $this->dao->prepare('SELECT * FROM '.$this->queueTable.' '.
            'WHERE execution_date <= NOW()'.
            'ORDER BY execution_date ASC LIMIT 1');

        $query->execute();

        if ($row = $query->fetch())
        {
            $job = unserialize($row['data']);
            if ($job == null)
            {
                $this->removeJob($row['id']);
                return null;
            }

            return [
                $row['id'],
                $row['max_attempts'],
                $job
            ];
        }

        return null;
    }

    protected function removeJob($id)
    {
        $this->dao->exec('DELETE FROM '.$this->queueTable.' WHERE id='.$id);
    }

    protected function handleFailedJob($job)
    {
        $this->dao->exec('INSERT INTO '.$this->failedTable.' (data) VALUES('.
            '\''.str_replace('\\', '\\\\', serialize($job)).'\')');
    }
}
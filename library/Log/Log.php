<?php

namespace Library\Log;

class Log
{
    const DEFAULT_LOG_FOLDER = 'Storage/Logs';

    protected $logFolder;

    public function __construct()
    {
        $this->logFolder = self::DEFAULT_LOG_FOLDER;
    }

    public function setLogFolder($str)
    {
        if (substr($str, -1) == '/')
        {
            $str = substr($str, 0, strlen($str) - 1);
        }

        $this->logFolder = $str;
    }

    public function getLogFolder()
    {
        return $this->logFolder;
    }

    public function info($message)
    {
        $this->writeLog('[info]'.$message);
    }

    public function error($message)
    {
        $this->writeLog('[error]'.$message);
    }

    protected function writeLog($message)
    {
        $fileName = $this->generateFileName();

        if (!$handle = fopen($fileName, 'a'))
        {
            return;
        }

        fwrite($handle, $this->getMessagePrefix().$message.PHP_EOL);

        fclose($handle);
    }

    protected function generateFileName()
    {
        return __DIR__.'/../../'.$this->logFolder.'/log-'.date('d-m-Y');
    }

    protected function getMessagePrefix()
    {
        return '['.date('d-m-Y G:i:s').']';
    }
}
<?php

namespace Library\Log;

class Log
{
    const LOG_FOLDER = 'Storage/Logs';

    public function info($message)
    {
        $this->writeLog($message);
    }

    protected function writeLog($message)
    {
        $fileName = $this->generateFileName();

        if (!$handle = fopen($fileName, 'a'))
        {
            return;
        }

        fwrite($handle, $message.PHP_EOL);

        fclose($handle);
    }

    protected function generateFileName()
    {
        return 'log-'.date('d-m-Y');
    }
}
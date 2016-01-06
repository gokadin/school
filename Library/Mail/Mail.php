<?php

namespace Library\Mail;

use Library\Mail\Drivers\MailgunDriver;

class Mail
{
    private $driver;

    public function __construct($config)
    {
        $this->initializeDriver($config);
    }

    private function initializeDriver($config)
    {
        switch ($config['driver'])
        {
            default:
                $this->driver = new MailgunDriver($config['mailgun']);
                break;
        }
    }
}
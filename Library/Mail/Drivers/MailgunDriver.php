<?php

namespace Library\Mail\Drivers;

use Mailgun\Mailgun;

class MailgunDriver
{
    /**
     * @var Mailgun
     */
    private $mailgun;

    /**
     * @var string
     */
    private $domain;

    public function __construct($config)
    {
        $this->mailgun = new Mailgun($config['secret']);
        $this->domain = $config['domain'];
    }

    public function sendMessage(array $data)
    {
        $this->mailgun->sendMessage($this->domain, $data);
    }
}
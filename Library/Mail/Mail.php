<?php

namespace Library\Mail;

use Library\Mail\Drivers\MailgunDriver;

class Mail
{
    /**
     * @var mixed
     */
    private $driver;

    /**
     * @var string
     */
    private $to;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $subject;

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

    public function send($route, array $data, Callable $callback)
    {
        $this->prepareMessage($route, $data, $callback);

        $this->sendMessage();
    }

    public function to($to)
    {
        $this->to = $to;
    }

    public function from($from)
    {
        $this->from = $from;
    }

    public function subject($subject)
    {
        $this->subject = $subject;
    }

    private function prepareMessage($route, array $data, Callable $callback)
    {
        $callback($this);
    }

    private function sendMessage()
    {
        $this->driver->sendMessage([
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
            'text' => 'testing...'
        ]);
    }
}
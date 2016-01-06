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
        $this->mailgun = new Mailgun($config['secret']);
        $this->domain = $config['domain'];
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
        $this->mailgun->sendMessage($this->domain, [
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
            'text' => 'testing...'
        ]);
    }
}
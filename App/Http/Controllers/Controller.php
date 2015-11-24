<?php

namespace App\Http\Controllers;

use App\Jobs\Job;
use Library\Controller\Controller as BackController;
use Library\Http\Response;
use Library\Http\View;
use Library\Queue\Queue;
use Library\Session\Session;

abstract class Controller extends BackController
{
    protected $view;
    protected $session;
    protected $response;
    protected $queue;

    public function __construct(View $view, Session $session, Response $response, Queue $queue)
    {
        $this->view = $view;
        $this->session = $session;
        $this->response = $response;
        $this->queue = $queue;
    }

    protected function dispatchJob(Job $job)
    {
        $this->queue->push($job);
    }
}
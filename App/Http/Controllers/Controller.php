<?php

namespace App\Http\Controllers;

use Library\Controller\Controller as BackController;
use Library\Controller\ValidatesRequests;
use Library\Http\Response;
use Library\Http\View;
use Library\Queue\DispatchesJobs;
use Library\Session\Session;

abstract class Controller extends BackController
{
    use ValidatesRequests, DispatchesJobs;

    protected $view;
    protected $session;
    protected $response;

    public function __construct(View $view, Session $session, Response $response)
    {
        $this->view = $view;
        $this->session = $session;
        $this->response = $response;
    }
}
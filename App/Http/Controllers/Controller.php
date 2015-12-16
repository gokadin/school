<?php

namespace App\Http\Controllers;

use Library\Controller\Controller as BackController;
use Library\Http\Response;
use Library\Http\View;
use Library\Session\Session;

abstract class Controller extends BackController
{
    protected $view;
    protected $session;
    protected $response;
    protected $queue;

    public function __construct(View $view, Session $session, Response $response)
    {
        $this->view = $view;
        $this->session = $session;
        $this->response = $response;
    }
}
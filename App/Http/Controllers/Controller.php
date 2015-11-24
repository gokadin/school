<?php

namespace App\Http\Controllers;

use Library\Controller\Controller as BackController;
use Library\Controller\ValidatesRequests;
use Library\Http\Redirect;
use Library\Http\View;
use Library\Queue\DispatchesJobs;
use Library\Session\Session;

abstract class Controller extends BackController
{
    use ValidatesRequests, DispatchesJobs;

    protected $view;
    protected $session;
    protected $redirect;

    public function __construct(View $view, Session $session, Redirect $redirect)
    {
        $this->view = $view;
        $this->session = $session;
        $this->redirect = $redirect;
    }
}
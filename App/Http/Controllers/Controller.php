<?php

namespace App\Http\Controllers;

use Library\Controller\Controller as BackController;
use Library\Http\Response;
use Library\Http\View;

abstract class Controller extends BackController
{
    protected $view;
    protected $response;

    public function __construct(View $view, Response $response)
    {
        $this->view = $view;
        $this->response = $response;
    }
}
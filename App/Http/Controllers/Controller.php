<?php

namespace App\Http\Controllers;

use Library\Controller\Controller as BackController;
use Library\Controller\Traits\ValidatesRequests;

abstract class Controller extends BackController
{
    use ValidatesRequests;
}
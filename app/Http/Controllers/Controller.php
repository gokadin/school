<?php

namespace App\Http\Controllers;

use Library\Controller\Controller as BackController;
use Library\Controller\ValidatesRequests;
use Library\Queue\DispatchesJobs;

abstract class Controller extends BackController
{
    use ValidatesRequests, DispatchesJobs;
}
<?php

namespace App\Http\Controllers;

use Library\Controller\BackController;

abstract class Controller extends BackController
{
    use ValidatesRequests;
}
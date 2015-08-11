<?php

namespace App\Http\Requests;

use Library\Http\Request as HttpRequest;
use Library\Http\RequestContract;

abstract class Request extends HttpRequest implements RequestContract
{

}
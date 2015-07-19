<?php

namespace App\Http\Controllers;

use Library\Facades\Request;

class TestController extends Controller
{
    public function index()
    {


        return Request::data('id');
    }
}
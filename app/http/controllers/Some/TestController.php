<?php

namespace App\Http\Controllers\Some;


use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public function index()
    {
        return 'inside some';
    }
}
<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return view('frontend.index', ['one' => 'something'])
            ->with('two', 'something2')
            ->with(['three' => 'something3']);
    }
}
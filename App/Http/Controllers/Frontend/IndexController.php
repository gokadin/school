<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return view('frontend.index');
    }

    public function features()
    {
        return view('frontend.features');
    }

    public function testimonials()
    {
        return view('frontend.testimonials');
    }

    public function faq()
    {
        return view('frontend.faq');
    }

    public function about()
    {
        return view('frontend.about');
    }
}
<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return $this->view->make('frontend.index.index');
    }

    public function features()
    {
        return $this->view->make('frontend.index.features');
    }

    public function testimonials()
    {
        return $this->view->make('frontend.index.testimonials');
    }

    public function faq()
    {
        return $this->view->make('frontend.index.faq');
    }

    public function about()
    {
        return $this->view->make('frontend.index.about');
    }
}
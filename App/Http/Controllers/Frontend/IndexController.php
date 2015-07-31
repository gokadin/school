<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Library\Events\FiresEvents;
use Library\Queue\DispatchesJobs;

class IndexController extends Controller
{
    use FiresEvents, DispatchesJobs;

    public function index()
    {
        return view('frontend.index.index');
    }

    public function features()
    {
        return view('frontend.index.features');
    }

    public function testimonials()
    {
        return view('frontend.index.testimonials');
    }

    public function faq()
    {
        return view('frontend.index.faq');
    }

    public function about()
    {
        return view('frontend.index.about');
    }
}
<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Jobs\Frontend\SendWelcomeEmail;
use Library\Facades\Queue;
use Models\TempTeacher;

class IndexController extends Controller
{
    public function index()
    {
        $t = TempTeacher::find(1);

        Queue::push(new SendWelcomeEmail($t));

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
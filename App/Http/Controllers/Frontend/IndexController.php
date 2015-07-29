<?php

namespace App\Http\Controllers\Frontend;

use App\Events\Frontend\TeacherPreRegistered;
use App\Http\Controllers\Controller;
use App\Jobs\Frontend\PreRegisterTeacher;
use Library\Events\FiresEvents;
use Library\Queue\DispatchesJobs;
use Models\TempTeacher;

class IndexController extends Controller
{
    use FiresEvents, DispatchesJobs;

    public function index()
    {
        $this->dispatchJob(new PreRegisterTeacher([
            'subscription_id' => 1,
            'first_name' => 'jake',
            'last_name' => 'popo',
            'email' => 'a@b.com',
            'confirmation_code' => '123'
        ]));

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
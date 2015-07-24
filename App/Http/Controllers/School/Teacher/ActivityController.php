<?php

namespace App\Http\Controllers\School\Teacher;

use App\Http\Controllers\Controller;
use Library\Facades\Redirect;
use Library\Facades\Sentry;
use Library\Facades\Session;
use Library\Facades\Request;
use Models\Activity;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::where('teacher_id', '=', Sentry::user()->id)->get();

        return view('school.teacher.activity.index', ['activities' => $activities]);
    }

    public function create()
    {
        return view('school.teacher.activity.create');
    }

    public function store()
    {
        $this->validateRequest([
            'name' => 'required',
            'defaultRate' => [
                'required' => 'default rate is required',
                'numeric' => 'default rate must be numeric'
            ]
        ]);

        $activity = Activity::create([
            'teacher_id' => Sentry::user()->id,
            'name' => Request::data('name'),
            'rate' => Request::data('defaultRate'),
            'period' => Request::data('period'),
            'location' => Request::data('location')
        ]);

        if ($activity == null)
        {
            Session::setFlash('An error occurred. Activity was not created.');
            Redirect::back();
            return;
        }

        Session::setFlash('Activity <b>'.$activity->name.'</b> was created successfully.');

        if (Request::data('createAnother') == 1)
        {
            Redirect::to('school.teacher.activity.create');
            return;
        }

        Redirect::to('school.teacher.activity.index');
    }
}
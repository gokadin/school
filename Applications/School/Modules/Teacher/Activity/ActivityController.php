<?php namespace Applications\School\Modules\Teacher\Activity;

use Library\BackController;
use Library\Facades\Page;
use Library\Facades\Request;
use Library\Facades\Response;
use Library\Facades\Session;
use Models\Activity;

class ActivityController extends BackController
{
    public function index()
    {
        Page::add('activities', Activity::all());
    }

    public function create()
    {

    }

    public function store()
    {
        $activity = Activity::create([
            'teacher_id' => $this->currentUser->id,
            'name' => Request::postData('name'),
            'rate' => Request::postData('defaultRate'),
            'period' => Request::postData('period'),
            'location' => Request::postData('location')
        ]);

        if ($activity == null)
            Session::setFlash('An error occurred. Activity was not created.');
        else
            Session::setFlash('Activity <b>'.$activity->name.'</b> was created successfully.');

        if (Request::postData('createAnother') == 1)
            Response::toAction('School#Teacher/Activity#create');

        Response::toAction('School#Teacher/Activity#index');
    }

    public function edit()
    {

    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}
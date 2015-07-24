<?php namespace Applications\School\Modules\Teacher\Activity;

use Library\BackController;
use Library\Facades\Page;
use Library\Facades\Request;
use Library\Facades\Response;
use Library\Facades\Session;
use Models\Activity;

class ActivityController extends BackController
{
    public function update()
    {
        if (!is_numeric(Request::data('activityId')) || !Activity::exists('id', Request::data('activityId')))
        {
            Session::setFlash('An error occurred. Activity is not valid.');
            Response::toAction('School#Teacher/Activity#index');
        }

        $activity = Activity::find(Request::data('activityId'));
        $activity->name = Request::data('name');
        $activity->rate = Request::data('defaultRate');
        $activity->period = Request::data('period');
        $activity->location = Request::data('location');

        if ($activity->save())
            Session::setFlash('Updated activity <b>'.$activity->name.'</b>.');
        else
            Session::setFlash('An error occurred. Could not update activity.');

        Response::toAction('School#Teacher/Activity#index');
    }

    public function destroy()
    {
        if (!is_numeric(Request::data('activityId')) || !Activity::exists('id', Request::data('activityId')))
        {
            Session::setFlash('An error occurred. Activity is not valid.');
            Response::toAction('School#Teacher/Activity#index');
        }

        $activity = Activity::find(Request::data('activityId'));
        if ($activity->delete())
            Session::setFlash('Deleted activity <b>'.$activity->name.'</b>.');
        else
            Session::setFlash('An error occurred. Could not delete activity.');

        Response::toAction('School#Teacher/Activity#index');
    }
}
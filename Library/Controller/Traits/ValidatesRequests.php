<?php

namespace Library\Controller\Traits;

use Library\Facades\Redirect;
use Library\Facades\Request;
use Library\Facades\Validator;

trait ValidatesRequests
{
    public function validateRequest(array $rules, $handleErrors = true)
    {
        if ($rules == null || sizeof($rules) == 0)
            return true;

        if (!Validator::make(Request::all(), $rules, $handleErrors))
        {
            if ($handleErrors)
                Redirect::back();
            else
                return false;
        }

        return true;
    }
}
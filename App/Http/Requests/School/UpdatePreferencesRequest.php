<?php

namespace App\Http\Requests\School;

use App\Http\Requests\Request;

class UpdatePreferencesRequest extends Request
{
    function authorize()
    {
        return true;
    }

    function rules()
    {
        return true;
    }
}
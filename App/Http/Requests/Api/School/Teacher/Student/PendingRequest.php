<?php

namespace App\Http\Requests\Api\School\Teacher\Student;

use App\Http\Requests\Request;

class PendingRequest extends Request
{
    function authorize()
    {
        return true;
    }

    function rules()
    {
        return [];
    }
}
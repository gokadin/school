<?php

namespace App\Http\Requests\Api\School\Teacher\Student;

use App\Http\Requests\Request;

class PaginateRequest extends Request
{
    function authorize()
    {
        return true;
    }

    function rules()
    {
        return [
            'page' => ['required', 'numeric'],
            'max' => ['required', 'numeric']
        ];
    }
}
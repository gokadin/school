<?php

namespace App\Http\Requests\Api\School;

use App\Http\Requests\Request;

class GetTeacherActivitiesRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'page' => ['required', 'numeric'],
            'max' => ['required', 'numeric']
        ];
    }
}
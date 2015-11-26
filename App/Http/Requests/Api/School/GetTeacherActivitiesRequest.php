<?php

namespace App\Http\Requests\Api\School;

use Library\Http\Request;

class GetTeacherActivitiesRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'currentPage' => ['required', 'numeric'],
            'max' => ['required', 'numeric'],
            'sortBy' => 'required',
            'sortAscending' => 'required',
            'filters' => 'required'
        ];
    }
}
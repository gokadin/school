<?php

namespace App\Http\Requests\Api\School;

use App\Http\Requests\AuthenticatedRequest;

class CreateEventRequest extends AuthenticatedRequest
{
    function authorize()
    {
        return true;
    }

    function rules()
    {
        return [
            'title' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'color' => 'required'
        ];
    }
}
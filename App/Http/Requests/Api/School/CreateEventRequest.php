<?php

namespace App\Http\Requests\Api\School;

use App\Http\Requests\AuthenticatedRequest;

class CreateEventRequest extends AuthenticatedRequest
{
    function authorize()
    {
        return true; // handle student ids not teacher's!
    }

    function rules()
    {
        return [
            'title' => 'required',
            'descriotion' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'startTime' => 'required',
            'endTime' => 'required',
            'isAllDay' => 'required',
            'color' => 'required',
            'activityId' => ['required', 'numeric']
        ];
    }
}
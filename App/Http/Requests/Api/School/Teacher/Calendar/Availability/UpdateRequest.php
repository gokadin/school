<?php

namespace App\Http\Requests\Api\School\Teacher\Calendar\Availability;

use App\Http\Requests\Request;

class UpdateRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required', 'numeric'],
            'date' => 'required',
            'startTime' => ['required', 'numeric'],
            'endTime' => ['required', 'numeric']
        ];
    }
}
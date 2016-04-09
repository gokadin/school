<?php

namespace App\Http\Requests\Api\School\Teacher\Calendar\Availability;

use App\Http\Requests\Request;

class StoreRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'date' => 'required',
            'startTime' => ['required', 'numeric'],
            'endTime' => ['required', 'numeric']
        ];
    }
}
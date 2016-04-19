<?php

namespace App\Http\Requests\Api\School\Teacher\Calendar\Availability;

use App\Http\Requests\Request;

class ApplyToFutureWeeksRequest extends Request
{
    function authorize(): bool
    {
        return true;
    }

    function rules(): array
    {
        return [
            'date' => 'required'
        ];
    }
}
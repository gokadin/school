<?php

namespace App\Http\Requests\Api\School\Teacher\Calendar\Availability;

use App\Http\Requests\AuthenticatedRequest;

class UpdateRequest extends AuthenticatedRequest
{
    public function authorize()
    {
        return $this->user->availabilities()->containsId($this->id);
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
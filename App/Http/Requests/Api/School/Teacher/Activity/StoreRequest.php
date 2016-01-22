<?php

namespace App\Http\Requests\Api\School\Teacher\Activity;

use App\Http\Requests\AuthenticatedRequest;

class StoreRequest extends AuthenticatedRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'max:25'],
            'rate' => ['required', 'numeric'],
            'period' => 'required',
            'location' => ['required', 'max:50']
        ];
    }
}

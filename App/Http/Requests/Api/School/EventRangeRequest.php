<?php

namespace App\Http\Requests\Api\School;

use App\Http\Requests\AuthenticatedRequest;

class EventRangeRequest extends AuthenticatedRequest
{
    function authorize()
    {
        return true;
    }

    function rules()
    {
        return [
            'from' => 'required',
            'to' => 'required'
        ];
    }
}
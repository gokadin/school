<?php

namespace App\Http\Requests\Api\School;

use App\Http\Requests\AuthenticatedRequest;

class ChangeEventDateRequest extends AuthenticatedRequest
{
    function authorize()
    {
        return true;
    }

    function rules()
    {
        return [
            'id' => ['required', 'numeric'],
            'newStartDate' => 'required'
        ];
    }
}
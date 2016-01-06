<?php

namespace App\Http\Requests\Api\School;

use App\Http\Requests\AuthenticatedRequest;

class SearchStudentRequest extends AuthenticatedRequest
{
    function authorize()
    {
        return true;
    }

    function rules()
    {
        return [
            'search' => 'required'
        ];
    }
}
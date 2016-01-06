<?php

namespace App\Http\Requests\Api\School;

use App\Http\Requests\AuthenticatedRequest;

class FromStudentIdsRequest extends AuthenticatedRequest
{
    function authorize()
    {
        return true;
    }

    function rules()
    {
        return [
            'studentIds' => 'required'
        ];
    }
}
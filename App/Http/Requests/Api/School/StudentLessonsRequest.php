<?php

namespace App\Http\Requests\Api\School;

use App\Http\Requests\AuthenticatedRequest;

class StudentLessonsRequest extends AuthenticatedRequest
{
    function authorize()
    {
        return $this->user->students()->containsId($this->get('id'));
    }

    function rules()
    {
        return true;
    }
}
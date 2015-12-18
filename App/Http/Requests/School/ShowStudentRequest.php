<?php

namespace App\Http\Requests\School;

use App\Http\Requests\AuthenticatedRequest;

class ShowStudentRequest extends AuthenticatedRequest
{
    public function authorize()
    {
        return $this->user()->students()->containsId($this->data('id'));
    }

    public function rules()
    {
        return true;
    }
}
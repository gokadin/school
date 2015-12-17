<?php

namespace App\Http\Requests\Frontend;

use App\Http\Requests\Request;

class RegisterStudentRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tempStudentId' => ['required', 'numeric']
        ];
    }
}
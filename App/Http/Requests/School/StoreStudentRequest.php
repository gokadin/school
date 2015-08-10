<?php

namespace App\Http\Requests\School;

use App\Http\Requests\Request;

class StoreStudentRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'firstName' => ['required' => 'first name is required'],
            'lastName' => ['required' => 'last name is required'],
            'email' => ['required', 'email', 'unique:Student,email', 'unique:Teacher,email'],
            'rate' => ['required', 'numeric']
        ];
    }
}
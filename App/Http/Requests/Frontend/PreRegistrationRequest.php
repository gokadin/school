<?php

namespace App\Http\Requests\Frontend;

use App\Http\Requests\Request;

class PreRegistrationRequest extends Request
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
            'email' => ['email', 'unique:Teacher,email', 'unique:Student,email'],
            'subscriptionType' => 'required'
        ];
    }
}
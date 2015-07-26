<?php

namespace App\Http\Requests\Frontend;

use App\Http\Requests\Request;

class RegistrationRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'password' => 'required',
            'confirmPassword' => ['required', 'equalsField:password' => 'passwords don\'t match']
        ];
    }
}
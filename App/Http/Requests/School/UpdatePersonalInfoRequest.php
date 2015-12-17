<?php

namespace App\Http\Requests\School;

use App\Http\Requests\Request;

class UpdatePersonalInfoRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => ['required', 'email']
        ];
    }
}
<?php

namespace App\Http\Requests\Api\Frontend\Account;

use App\Http\Requests\Request;

class LoginRequest extends Request
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => 'required'
        ];
    }
}
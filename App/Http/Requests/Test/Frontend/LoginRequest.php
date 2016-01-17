<?php

namespace App\Http\Requests\Test\Frontend;

use App\Http\Requests\Request;

class LoginRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return true;
    }
}

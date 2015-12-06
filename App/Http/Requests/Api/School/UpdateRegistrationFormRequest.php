<?php

namespace App\Http\Requests\Api\School;

use App\Http\Requests\Request;

class UpdateRegistrationFormRequest extends Request
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
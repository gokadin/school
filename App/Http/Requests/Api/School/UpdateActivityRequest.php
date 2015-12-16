<?php

namespace App\Http\Requests\Api\School;

use App\Http\Requests\Request;

class UpdateActivityRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'rate' => ['required', 'numeric'],
            'period' => ['required']
        ];
    }
}
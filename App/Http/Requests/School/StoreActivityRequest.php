<?php

namespace App\Http\Requests\School;

use App\Http\Requests\Request;

class StoreActivityRequest extends Request
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
            'period' => 'required'
        ];
    }
}
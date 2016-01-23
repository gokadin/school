<?php

namespace App\Http\Requests\Api\School\Teacher\Event;

use App\Http\Requests\Request;

class RangeRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'from' => 'required',
            'to' => 'required'
        ];
    }
}
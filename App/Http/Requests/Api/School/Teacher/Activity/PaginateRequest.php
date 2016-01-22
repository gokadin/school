<?php

namespace App\Http\Requests\Api\School\Teacher\Activity;

use App\Http\Requests\Request;

class PaginateRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'page' => ['required', 'numeric'],
            'max' => ['required', 'numeric']
        ];
    }
}

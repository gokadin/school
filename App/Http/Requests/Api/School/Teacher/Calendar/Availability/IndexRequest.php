<?php

namespace App\Http\Requests\Api\School\Teacher\Calendar\Availability;

use App\Http\Requests\Request;

class IndexRequest extends Request
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fromDate' => 'required',
            'toDate' => 'required'
        ];
    }
}
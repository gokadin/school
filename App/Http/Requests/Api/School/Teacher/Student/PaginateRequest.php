<?php

namespace App\Http\Requests\Api\School\Teacher\Student;

use App\Http\Requests\Request;

class PaginateRequest extends Request
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['required', 'numeric'],
            'max' => ['required', 'numeric']
        ];
    }
}
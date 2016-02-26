<?php

namespace App\Http\Requests\Api\School\Teacher\Student;

use App\Http\Requests\Request;

class PendingRequest extends Request
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
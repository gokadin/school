<?php

namespace App\Http\Requests\Api\School\Teacher\Student;

use App\Http\Requests\AuthenticatedRequest;

class ShowRequest extends AuthenticatedRequest
{
    public function authorize(): bool
    {
        return $this->user->students()->containsId($this->id);
    }

    public function rules(): array
    {
        return [];
    }
}
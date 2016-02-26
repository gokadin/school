<?php

namespace App\Http\Requests\Api\School\Teacher\Activity;

use App\Http\Requests\AuthenticatedRequest;

class StudentsRequest extends AuthenticatedRequest
{
    public function authorize(): bool
    {
        return $this->user->activities()->containsId($this->id);
    }

    public function rules(): array
    {
        return [];
    }
}
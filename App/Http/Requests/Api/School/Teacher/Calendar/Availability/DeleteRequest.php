<?php

namespace App\Http\Requests\Api\School\Teacher\Calendar\Availability;

use App\Http\Requests\AuthenticatedRequest;

class DeleteRequest extends AuthenticatedRequest
{
    public function authorize(): bool
    {
        return $this->user->availabilities()->containsId($this->id);
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'numeric']
        ];
    }
}
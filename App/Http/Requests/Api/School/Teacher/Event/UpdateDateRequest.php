<?php

namespace App\Http\Requests\Api\School\Teacher\Event;

use App\Http\Requests\AuthenticatedRequest;

class UpdateDateRequest extends AuthenticatedRequest
{
    public function authorize(): bool
    {
        return $this->user->events()->containsId($this->id);
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'numeric'],
            'oldDate' => ['required', 'date'],
            'date' => ['required', 'date']
        ];
    }
}

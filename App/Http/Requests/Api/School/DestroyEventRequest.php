<?php

namespace App\Http\Requests\Api\School;

use App\Http\Requests\AuthenticatedRequest;

class DestroyEventRequest extends AuthenticatedRequest
{
    function authorize()
    {
        return $this->user()->events()->containsId($this->get('id'));
    }

    function rules()
    {
        return [
            'id' => ['required', 'numeric']
        ];
    }
}
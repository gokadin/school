<?php

namespace App\Http\Requests\Api\Frontend\Account;

use App\Http\Requests\Request;

class CurrentUserRequest extends Request
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
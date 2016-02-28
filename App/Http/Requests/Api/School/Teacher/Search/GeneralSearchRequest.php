<?php

namespace App\Http\Requests\Api\School\Teacher\Search;

use App\Http\Requests\Request;

class GeneralSearchRequest extends Request
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
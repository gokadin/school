<?php

namespace App\Http\Requests\School;

use App\Http\Requests\Request;

class StoreMessageRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'conversation_id' => ['required', 'numeric'],
            'content' => 'required'
        ];
    }
}
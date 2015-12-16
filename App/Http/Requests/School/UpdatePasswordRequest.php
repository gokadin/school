<?php

namespace App\Http\Requests\School;

use App\Http\Requests\Request;

class UpdatePasswordRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'currentPassword' => 'required',
            'newPassword' => 'required',
            'confirmPassword' => 'equalsField:newPassword'
        ];
    }
}
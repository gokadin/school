<?php

namespace App\Http\Requests\School;

use App\Http\Requests\Request;

class PreRegisterStudentRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
//            'email' => [
//                'email',
//                'unique:teachers,email',
//                'unique:students,email',
//                'unique:temp_teachers,email',
//                'unique:temp_students,email'
//            ],
            'activityId' => ['required', 'numeric']
        ];
    }
}
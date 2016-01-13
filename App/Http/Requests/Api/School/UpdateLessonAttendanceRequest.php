<?php

namespace App\Http\Requests\Api\School;

use App\Http\Requests\AuthenticatedRequest;

class UpdateLessonAttendanceRequest extends AuthenticatedRequest
{
    function authorize()
    {
        return $this->user->events()->containsId($this->get('eventId'))
            && $this->user->events()->find($this->get('eventId'))->lessons()->containsId($this->get('lessonId'));
    }

    function rules()
    {
        return [
            'date' => 'required',
            'attended' => 'required'
        ];
    }
}
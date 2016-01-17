<?php

namespace App\Http\Controllers\Test\Api\School\Teacher;

use App\Http\Controllers\Api\ApiController;

class MessagingController extends ApiController
{
    public function students()
    {
        return $this->respondOk([
            'students' => [
                'John', 'Dave', 'Melissa'
            ]
        ]);
    }
}
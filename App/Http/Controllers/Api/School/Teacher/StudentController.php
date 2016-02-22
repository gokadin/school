<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\School\Teacher\Student\PaginateRequest;
use App\Http\Requests\Api\School\Teacher\Student\PendingRequest;
use App\Http\Translators\Api\School\Teacher\Student\PaginateTranslator;
use App\Http\Translators\Api\School\Teacher\Student\PendingTranslator;
use Library\Http\Response;

class StudentController extends ApiController
{
    public function paginate(PaginateRequest $request, PaginateTranslator $translator): Response
    {
        $data = $translator->translateRequest($request);

        return $data ? $this->respondOk($data) : $this->respondBadRequest();
    }

    public function pending(PendingRequest $request, PendingTranslator $translator): Response
    {
        $data = $translator->translateRequest($request);

        return $data ? $this->respondOk($data) : $this->respondBadRequest();
    }
}
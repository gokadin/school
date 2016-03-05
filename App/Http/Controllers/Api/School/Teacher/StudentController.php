<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\School\Teacher\Student\LessonsRequest;
use App\Http\Requests\Api\School\Teacher\Student\PaginateRequest;
use App\Http\Requests\Api\School\Teacher\Student\PendingRequest;
use App\Http\Requests\Api\School\Teacher\Student\ShowRequest;
use App\Http\Translators\Api\School\Teacher\Student\LessonsTranslator;
use App\Http\Translators\Api\School\Teacher\Student\PaginateTranslator;
use App\Http\Translators\Api\School\Teacher\Student\PendingTranslator;
use App\Http\Translators\Api\School\Teacher\Student\ShowTranslator;
use Library\Http\Response;

class StudentController extends ApiController
{
    public function paginate(PaginateRequest $request, PaginateTranslator $translator): Response
    {
        return $this->respond($translator->translateRequest($request));
    }

    public function pending(PendingRequest $request, PendingTranslator $translator): Response
    {
        return $this->respond($translator->translateRequest($request));
    }

    public function show(ShowRequest $request, ShowTranslator $translator): Response
    {
        return $this->respond($translator->translateRequest($request));
    }

    public function lessons(LessonsRequest $request, LessonsTranslator $translator): Response
    {
        return $this->respond($translator->translateRequest($request));
    }
}
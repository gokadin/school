<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\School\Teacher\Search\GeneralSearchRequest;
use App\Http\Translators\Api\School\Teacher\Search\GeneralSearchTranslator;
use Library\Http\Response;

class SearchController extends ApiController
{
    public function generalSearch(GeneralSearchRequest $request, GeneralSearchTranslator $translator): Response
    {
        return $this->respond($translator->translateRequest($request));
    }
}
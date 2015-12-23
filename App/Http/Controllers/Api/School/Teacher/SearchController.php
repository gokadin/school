<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Domain\Services\SearchService;
use App\Http\Controllers\Api\ApiController;

class SearchController extends ApiController
{
    public function index(SearchService $searchService, $search)
    {
        return $this->respondOk($searchService->searchAllForTeacher(urldecode($search)));
    }
}
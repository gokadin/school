<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Domain\Services\SearchService;
use App\Http\Controllers\Api\ApiController;
use Library\Http\Response;
use Library\Http\View;
use Library\Session\Session;

class SearchController extends ApiController
{
    /**
     * @var SearchService
     */
    private $searchService;

    public function __construct(View $view, Session $session, Response $response, SearchService $searchService)
    {
        parent::__construct($view, $session, $response);

        $this->searchService = $searchService;
    }

    public function index($search)
    {
        return $this->respondOk($this->searchService->searchAllForTeacher(urldecode($search)));
    }
}
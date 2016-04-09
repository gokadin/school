<?php

namespace App\Http\Controllers\Api\School\Teacher\Calendar;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\School\Teacher\Calendar\Availability\IndexRequest;
use App\Http\Requests\Api\School\Teacher\Calendar\Availability\StoreRequest;
use App\Http\Translators\Api\School\Teacher\Calendar\Availability\IndexTranslator;
use App\Http\Translators\Api\School\Teacher\Calendar\Availability\StoreTranslator;

class AvailabilityController extends ApiController
{
    public function index(IndexRequest $request, IndexTranslator $translator)
    {
        return ['lala' => 'hello'];
    }

    public function store(StoreRequest $request, StoreTranslator $translator)
    {
        return ['id' => 1];
    }
}
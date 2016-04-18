<?php

namespace App\Http\Controllers\Api\School\Teacher\Calendar;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\School\Teacher\Calendar\Availability\DestroyRequest;
use App\Http\Requests\Api\School\Teacher\Calendar\Availability\FetchRequest;
use App\Http\Requests\Api\School\Teacher\Calendar\Availability\StoreRequest;
use App\Http\Requests\Api\School\Teacher\Calendar\Availability\UpdateRequest;
use App\Http\Translators\Api\School\Teacher\Calendar\Availability\DestroyTranslator;
use App\Http\Translators\Api\School\Teacher\Calendar\Availability\FetchTranslator;
use App\Http\Translators\Api\School\Teacher\Calendar\Availability\StoreTranslator;
use App\Http\Translators\Api\School\Teacher\Calendar\Availability\UpdateTranslator;

class AvailabilityController extends ApiController
{
    public function fetch(FetchRequest $request, FetchTranslator $translator)
    {
        return $this->respond($translator->translateRequest($request));
    }

    public function store(StoreRequest $request, StoreTranslator $translator)
    {
        return $this->respond($translator->translateRequest($request));
    }

    public function update(UpdateRequest $request, UpdateTranslator $translator)
    {
        $translator->translateRequest($request);

        return $this->respondOk();
    }

    public function destroy(DestroyRequest $request, DestroyTranslator $translator)
    {
        $translator->translateRequest($request);

        return $this->respondOk();
    }
}
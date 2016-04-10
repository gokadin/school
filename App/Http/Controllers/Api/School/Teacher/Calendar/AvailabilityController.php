<?php

namespace App\Http\Controllers\Api\School\Teacher\Calendar;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\School\Teacher\Calendar\Availability\DeleteRequest;
use App\Http\Requests\Api\School\Teacher\Calendar\Availability\IndexRequest;
use App\Http\Requests\Api\School\Teacher\Calendar\Availability\StoreRequest;
use App\Http\Requests\Api\School\Teacher\Calendar\Availability\UpdateRequest;
use App\Http\Translators\Api\School\Teacher\Calendar\Availability\DeleteTranslator;
use App\Http\Translators\Api\School\Teacher\Calendar\Availability\IndexTranslator;
use App\Http\Translators\Api\School\Teacher\Calendar\Availability\StoreTranslator;
use App\Http\Translators\Api\School\Teacher\Calendar\Availability\UpdateTranslator;

class AvailabilityController extends ApiController
{
    public function range(IndexRequest $request, IndexTranslator $translator)
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

    public function destroy(DeleteRequest $request, DeleteTranslator $translator)
    {
        $translator->translateRequest($request);

        return $this->respondOk();
    }
}
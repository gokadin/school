<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Domain\Services\EventService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\School\CreateEventRequest;
use App\Http\Requests\Api\School\EventRangeRequest;

class EventController extends ApiController
{
    public function create(CreateEventRequest $request, EventService $eventService)
    {
        if (!$eventService->create($request->all()))
        {
            return $this->respondBadRequest();
        }

        return $this->respondOk();
    }

    public function range(EventRangeRequest $request, EventService $eventService)
    {
        return $this->respondOk(['events' => $eventService->range($request->all())]);
    }
}
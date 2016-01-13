<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Domain\Services\EventService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\School\ChangeEventDateRequest;
use App\Http\Requests\Api\School\CreateEventRequest;
use App\Http\Requests\Api\School\DestroyEventRequest;
use App\Http\Requests\Api\School\EventRangeRequest;

class EventController extends ApiController
{
    public function create(CreateEventRequest $request, EventService $eventService)
    {
        $eventId = $eventService->create($request->all());
        if (!$eventId)
        {
            return $this->respondBadRequest();
        }

        return $this->respondOk(['eventId' => $eventId]);
    }

    public function range(EventRangeRequest $request, EventService $eventService)
    {
        return $this->respondOk(['events' => $eventService->range($request->all())]);
    }

    public function changeDate(ChangeEventDateRequest $request, EventService $eventService)
    {
        return $this->respondOk($eventService->changeDate($request->all()));
    }

    public function upcomingEvents(EventService $eventService)
    {
        return ['upcomingEvents' => $eventService->upcomingEvents()];
    }

    public function destroy(DestroyEventRequest $request, EventService $eventService)
    {
        $eventService->destroy($request->get('id'));

        return $this->respondOk();
    }
}
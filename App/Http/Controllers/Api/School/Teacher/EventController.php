<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Domain\Services\EventService;
use App\Domain\Services\LessonService;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\School\CreateEventRequest;
use App\Http\Requests\Api\School\DestroyEventRequest;
use App\Http\Requests\Api\School\Teacher\Event\RangeRequest;
use App\Http\Requests\Api\School\Teacher\Event\UpdateDateRequest;
use App\Http\Requests\Api\School\UpdateLessonAttendanceRequest;
use App\Http\Translators\Api\School\Teacher\Event\RangeTranslator;
use App\Http\Translators\Api\School\Teacher\Event\UpdateDateTranslator;
use Library\Http\Response;

class EventController extends ApiController
{
    public function create(CreateEventRequest $request, EventService $eventService)
    {
        $events = $eventService->create($request->all());
        if (!$events)
        {
            return $this->respondBadRequest();
        }

        return $this->respondOk(['events' => $events]);
    }

    public function range(RangeRequest $request, RangeTranslator $translator): Response
    {
        $data = $translator->translateRequest($request);

        return $data ? $this->respondOk($data) : $this->respondBadRequest();
    }

    public function updateDate(UpdateDateRequest $request, UpdateDateTranslator $translator)
    {
        $data = $translator->translateRequest($request);

        return $data ? $this->respondOk($data) : $this->respondBadRequest();
    }

    public function upcomingEvents(EventService $eventService)
    {
        return $eventService->upcomingEvents();
    }

    public function destroy(DestroyEventRequest $request, EventService $eventService)
    {
        $eventService->destroy($request->get('id'));

        return $this->respondOk();
    }

    public function updateLessonAttendance(UpdateLessonAttendanceRequest $request, LessonService $lessonService)
    {
        $lessonService->updateAttendance($request->all());

        return $this->respondOk();
    }
}
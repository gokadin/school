<?php

namespace App\Http\Translators\Api\School\Teacher\Event;

use App\Domain\Events\Event;
use App\Domain\Services\EventService;
use Carbon\Carbon;
use Library\Http\Request;
use App\Http\Translators\Translator;
use Library\Transformer\Transformer;

class UpdateDateTranslator extends Translator
{
    /**
     * @var EventService
     */
    private $eventService;

    public function __construct(Transformer $transformer, EventService $eventService)
    {
        parent::__construct($transformer);

        $this->eventService = $eventService;
    }

    public function translateRequest(Request $request): array
    {
        return $this->translateResponse($this->eventService->updateDate(
            $request->id, Carbon::parse($request->oldDate), Carbon::parse($request->date)));
    }

    public function translateResponse(Event $event): array
    {
        return $this->transformer->of(Event::class)->transform($event);
    }
}

<?php

namespace App\Http\Translators\School\Teacher\Event;

use App\Domain\Services\EventService;
use App\Domain\Users\Authenticator;
use App\Http\Translators\School\AuthenticatedTranslator;
use Carbon\Carbon;
use Library\Http\Request;
use Library\Transformer\Transformer;

class RangeTranslator extends AuthenticatedTranslator
{
    /**
     * @var EventService
     */
    private $eventService;

    public function __construct(Authenticator $authenticator, Transformer $transformer, EventService $eventService)
    {
        parent::__construct($authenticator, $transformer);

        $this->eventService = $eventService;
    }

    public function translateRequest(Request $request): array
    {
        return $this->translateResponse($this->eventService->range(
            $this->user, Carbon::parse($request->from), Carbon::parse($request->to)));
    }

    private function translateResponse(array $data): array
    {
        // already transformed by event service
        return $data;
    }
}
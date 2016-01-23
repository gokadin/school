<?php

namespace App\Http\Translators\School\Teacher\Activity;

use App\Domain\Activities\Activity;
use App\Domain\Services\ActivityService;
use App\Domain\Users\Authenticator;
use App\Http\Translators\School\AuthenticatedTranslator;
use Library\Http\Request;
use Library\Transformer\Transformer;

class PaginateTranslator extends AuthenticatedTranslator
{
    /**
     * @var ActivityService
     */
    private $activityService;

    public function __construct(Authenticator $authenticator, Transformer $transformer, ActivityService $activityService)
    {
        parent::__construct($authenticator, $transformer);

        $this->activityService = $activityService;
    }

    public function translateRequest(Request $request): array
    {
        return $this->translateResponse($this->activityService->paginate(
            $this->user, $request->page, $request->max, $request->sort ?? [], $request->search ?? []));
    }

    public function translateResponse(array $data): array
    {
        return [
            'activities' => $this->transformer->of(Activity::class)->transform($data['data']),
            'pagination' => $data['pagination']
        ];
    }
}
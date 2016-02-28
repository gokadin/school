<?php

namespace App\Http\Translators\Api\School\Teacher\Search;

use App\Domain\Activities\Activity;
use App\Domain\Services\SearchService;
use App\Domain\Users\Authenticator;
use App\Domain\Users\Student;
use App\Http\Translators\AuthenticatedTranslator;
use Library\Http\Request;
use Library\Transformer\Transformer;

class GeneralSearchTranslator extends AuthenticatedTranslator
{
    /**
     * @var SearchService
     */
    private $searchService;

    public function __construct(Authenticator $authenticator, Transformer $transformer, SearchService $searchService)
    {
        parent::__construct($authenticator, $transformer);

        $this->searchService = $searchService;
    }

    public function translateRequest(Request $request): array
    {
        return $this->translateResponse($this->searchService->generalSearch($this->user, $request->search));
    }

    private function translateResponse(array $data): array
    {
        return [
            'students' => $this->transformer->of(Student::class)->only(['id', 'firstName', 'lastName'])
                ->transform($data['students']),

            'activities' => $this->transformer->of(Activity::class)->only(['id', 'name'])
                ->transform($data['activities'])
        ];
    }
}
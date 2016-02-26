<?php

namespace App\Http\Translators\Api\School\Teacher\Activity;

use App\Domain\Services\ActivityService;
use App\Domain\Users\Authenticator;
use App\Domain\Users\Student;
use App\Http\Translators\AuthenticatedTranslator;
use Library\Http\Request;
use Library\Transformer\Transformer;

class StudentsTranslator extends AuthenticatedTranslator
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
        return $this->translateResponse($this->activityService->studentList($this->user, $request->id));
    }

    private function translateResponse(array $data): array
    {
        return [
            'students' => $this->transformer->of(Student::class)->transform($data)
        ];
    }
}
<?php

namespace App\Http\Translators\Api\School\Teacher\Student;

use App\Domain\Events\Lesson;
use App\Domain\Services\LessonService;
use App\Domain\Services\StudentService;
use App\Domain\Users\Authenticator;
use App\Http\Translators\AuthenticatedTranslator;
use Carbon\Carbon;
use Library\Http\Request;
use Library\Transformer\Transformer;

class LessonsTranslator extends AuthenticatedTranslator
{
    /**
     * @var LessonService
     */
    private $lessonService;

    public function __construct(Authenticator $authenticator, Transformer $transformer, LessonService $lessonService)
    {
        parent::__construct($authenticator, $transformer);

        $this->lessonService = $lessonService;
    }

    public function translateRequest(Request $request): array
    {
        return $this->translateResponse($this->lessonService->getLessons($this->user->students()->find(
                $request->id), Carbon::parse($request->from), Carbon::parse($request->to)));
    }

    private function translateResponse(array $data): array
    {
        return [
            'lessons' => $data
        ];
        return $data;
        return $this->transformer->of(Lesson::class)->transform($data);
    }
}
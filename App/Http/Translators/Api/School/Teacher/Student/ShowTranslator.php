<?php

namespace App\Http\Translators\Api\School\Teacher\Student;

use App\Domain\Services\StudentService;
use App\Domain\Users\Authenticator;
use App\Domain\Users\Student;
use App\Http\Translators\AuthenticatedTranslator;
use Library\Http\Request;
use Library\Transformer\Transformer;

class ShowTranslator extends AuthenticatedTranslator
{
    /**
     * @var StudentService
     */
    private $studentService;

    public function __construct(Authenticator $authenticator, Transformer $transformer, StudentService $studentService)
    {
        parent::__construct($authenticator, $transformer);

        $this->studentService = $studentService;
    }

    public function translateRequest(Request $request): array
    {
        return $this->translateResponse($this->studentService->getProfile($request->id));
    }

    private function translateResponse(array $data): array
    {
        $data['student'] = $this->transformer->of(Student::class)->transform($data['student']);

        return $data;
    }
}
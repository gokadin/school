<?php

namespace App\Http\Translators\Api\School\Teacher\Student;

use App\Domain\Services\StudentService;
use App\Domain\Users\Authenticator;
use App\Domain\Users\TempStudent;
use App\Http\Translators\AuthenticatedTranslator;
use Library\Http\Request;
use Library\Transformer\Transformer;

class PendingTranslator extends AuthenticatedTranslator
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
        return $this->translateResponse($this->studentService->pending($this->user));
    }

    public function translateResponse(array $data): array
    {
        return [
            'students' => $this->transformer->of(TempStudent::class)->transform($data)
        ];
    }
}
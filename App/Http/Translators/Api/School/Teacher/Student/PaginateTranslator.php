<?php

namespace App\Http\Translators\Api\School\Teacher\Student;

use App\Domain\Services\StudentService;
use App\Domain\Users\Authenticator;
use App\Domain\Users\Student;
use App\Http\Translators\AuthenticatedTranslator;
use Library\Http\Request;
use Library\Transformer\Transformer;

class PaginateTranslator extends AuthenticatedTranslator
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
        return $this->translateResponse($this->studentService->paginate(
            $this->user, $request->page, $request->max, $request->sort ?? [], $request->search ?? []));
    }

    public function translateResponse(array $data): array
    {
        return [
            'students' => $this->transformer->of(Student::class)->transform($data['data']),
            'pagination' => $data['pagination']
        ];
    }
}
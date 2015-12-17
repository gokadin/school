<?php

namespace App\Domain\Transformers;

class StudentTransformer extends Transformer
{
    public function transform($student, array $overrides = [])
    {
        return array_merge([
            'id' => $student->getId(),
            'firstName' => $student->firstName(),
            'lastName' => $student->lastName(),
            'email' => $student->email(),
            'activityName' => $student->activity()->name()
        ], $overrides);
    }
}
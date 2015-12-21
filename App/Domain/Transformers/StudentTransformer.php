<?php

namespace App\Domain\Transformers;

class StudentTransformer extends Transformer
{
    public function transform($student)
    {
        return $this->applyModifiers([
            'id' => $student->getId(),
            'firstName' => $student->firstName(),
            'lastName' => $student->lastName(),
            'fullName' => $student->name(),
            'email' => $student->email(),
            'activityName' => $student->activity()->name()
        ]);
    }
}
<?php

namespace App\Domain\Processors;

use App\Domain\Users\Student;
use Carbon\Carbon;

class RegistrationFormProcessor
{
    public function buildProfileInformation(Student $student)
    {
        $registrationForm = json_decode($student->teacher()->settings()->registrationForm(), true);

        $personal = [];
        foreach ($registrationForm['fields'] as $field)
        {
            if (!$field['active'])
            {
                continue;
            }

            if ($field['name'] == 'address')
            {
                continue;
            }

            $functionName = $field['name'];
            $value = $student->$functionName();

            if ($field['name'] == 'dateOfBirth')
            {
                $date = Carbon::parse($value);
                $age = $date->diffInYears();
                $value = $date->toDateString().' ('.$age.')';
            }

            $personal[] = [
                'display' => $field['displayName'],
                'value' => $value
            ];
        }

        $address = [];
        //$student->address();

        $extra = [];
        foreach ($registrationForm['extraFields'] as $field)
        {
            if (!$field['active'] || !isset($student->extraInfo()[$field['name']]))
            {
                continue;
            }

            $extra[] = ['display' => $field['displayName'], 'value' => $student->extraInfo()[$field['name']]];
        }

        return [
            'personal' => $personal,
            'address' => $address,
            'extra' => $extra
        ];
    }
}
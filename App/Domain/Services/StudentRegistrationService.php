<?php

namespace App\Domain\Services;

use App\Domain\Activities\Activity;
use App\Domain\Common\Address;
use App\Domain\Users\Student;
use App\Domain\Users\TempStudent;
use App\Events\Frontend\StudentRegistered;
use Carbon\Carbon;

class StudentRegistrationService extends AuthenticatedService
{
    public function validateTempStudent($id, $code)
    {
        $tempStudent = $this->repository->of(Student::class)->findTempStudent($id);

        if (is_null($tempStudent) || $tempStudent->confirmationCode() != $code || $tempStudent->isExpired())
        {
            return false;
        }

        return $tempStudent;
    }

    public function preparePreRegistrationData()
    {
        return [
            'activities' => json_encode($this->transformer->of(Activity::class)
                ->only(['id', 'name', 'rate'])->transform($this->user->activities()->toArray()))
        ];
    }

    public function prepareRegistrationData(TempStudent $tempStudent)
    {
        return [
            'registrationForm' => json_decode($tempStudent->teacher()->settings()->registrationForm(), true),
            'schoolName' => $tempStudent->teacher()->school()->name(),
            'teacherName' => $tempStudent->teacher()->name(),
            'activityName' => $tempStudent->activity()->name(),
            'firstName' => $tempStudent->firstName(),
            'lastName' => $tempStudent->lastName(),
            'tempStudentId' => $tempStudent->getId()
        ];
    }

    public function register(array $data)
    {
        $tempStudent = $this->repository->of(Student::class)->findTempStudent($data['tempStudentId']);
        if (is_null($tempStudent))
        {
            return false;
        }

        $form = json_decode($tempStudent->teacher()->settings()->registrationForm(), true);

        if (!$this->validateFormData($data, $form))
        {
            return false;
        }

        $address = new Address();
        foreach ($form['fields'] as $field)
        {
            if ($field['name'] == 'address' && $field['active'])
            {
                $address->setCountry($data['country']);
                $address->setCity($data['city']);
                $address->setStreet($data['address']);

                break;
            }
        }

        $student = new Student($data['firstName'], $data['lastName'], $tempStudent->email(), md5('admin'),
            $address, $tempStudent->activity(), $tempStudent->customPrice(), $tempStudent->teacher());

        foreach ($form['fields'] as $field)
        {
            if ($field['name'] == 'address')
            {
                continue;
            }

            if ($field['name'] == 'dateOfBirth')
            {
                $data[$field['name']] = Carbon::parse($data[$field['name']])->toDateString();
            }

            $setterName = 'set'.ucfirst($field['name']);
            $student->$setterName($data[$field['name']]);
        }

        $extraInfo = [];
        foreach ($form['extraFields'] as $field)
        {
            $extraInfo[$field['name']] = $data[$field['name']];
        }

        $student->setExtraInfo($extraInfo);

        $this->repository->of(Student::class)->register($student, $tempStudent);

        $this->fireEvent(new StudentRegistered($student));

        return true;
    }

    private function validateFormData(array $data, $form)
    {
        foreach ($form['fields'] as $field)
        {
            if ($field['name'] == 'address' && $field['active'])
            {
                if (!isset($data['country']) || !isset($data['city']) || !isset($data['address']))
                {
                    return false;
                }

                continue;
            }

            if ($field['active'] &&
                !isset($data[$field['name']]))
            {
                return false;
            }
        }

        foreach ($form['extraFields'] as $field)
        {
            if (!isset($data[$field['name']]))
            {
                return false;
            }
        }

        return true;
    }
}
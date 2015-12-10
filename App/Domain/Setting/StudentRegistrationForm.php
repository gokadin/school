<?php

namespace App\Domain\Setting;

class StudentRegistrationForm implements JsonSerializable
{
    private $requiredFields = [
        'firstName' => 'First name',
        'lastName' => 'Last name'
    ];

    /**
     * @var array ExtraField
     */
    private $extraFields = [];

    private $address;

    private $dateOfBirth;

    private $gender;

    private $occupation;

    public function extraFields()
    {
        return $this->extraFields;
    }

    public function setExtraFields($extraFields)
    {
        $this->extraFields = $extraFields;
    }

    public function address()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function dateOfBirth()
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    public function gender()
    {
        return $this->gender;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    public function occupation()
    {
        return $this->occupation;
    }

    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;
    }

    public static function defaultJson()
    {
        return json_encode([
            'firstName' => 'First name',
            'lastName' => 'Last name',
            'gender' => 'Gender',
            'extraFields' => []
        ]);
    }

    public static function MakeFromJson($json)
    {
        $result = json_decode($json);
    }

    public function generateJson()
    {
        $result = $this->requiredFields;

        if ($this->address) $result['address'] = 'Address';
        if ($this->dateOfBirth) $result['dateOfBirth'] = 'Date of birth';
        if ($this->gender) $result['gender'] = 'Gender';
        if ($this->occupation) $result['occupation'] = 'Occupation';

        foreach ($this->extraFields as $field)
        {
            $result['extraFields'][$field->name()] = $field->displayName();
        }

        return json_encode($result);
    }
}
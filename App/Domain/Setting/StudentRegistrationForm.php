<?php

namespace App\Domain\Setting;

use Library\DataMapper\Collection\EntityCollection;
use Library\DataMapper\DataMapperPrimaryKey;
use Library\DataMapper\DataMapperTimestamps;
use JsonSerializable;

/**
 * @Entity(name="student_registration_form")
 */
class StudentRegistrationForm implements JsonSerializable
{
    use DataMapperPrimaryKey, DataMapperTimestamps;

    /** @HasMany(target="App\Domain\Setting\FormField", mappedBy="form") */
    private $fields;

    /** @Column(type="boolean", default="0") */
    private $address;

    /** @Column(type="boolean", default="0") */
    private $dateOfBirth;

    /** @Column(type="boolean", default="0") */
    private $gender;

    /** @Column(type="boolean", default="0") */
    private $occupation;

    public function __construct()
    {
        $this->fields = new EntityCollection();
    }

    public function fields()
    {
        return $this->fields;
    }

    public function addField(FormField $field)
    {
        $this->fields->add($field);
    }

    public function removeField(FormField $field)
    {
        $this->fields->remove($field);
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

    public function defaultFields()
    {
        return [
            'firstName' => ['displayName' => 'First name', 'value' => 1],
            'lastName' => ['displayName' => 'Last name', 'value' => 1]
        ];
    }

    public function jsonSerialize()
    {
        return [
            'defaultFields' => $this->defaultFields(),
            'extraFields' => $this->fields,
            'regularFields' => [
                'address' => ['displayName' => 'Address', 'value' => $this->address],
                'dateOfBirth' => ['displayName' => 'Date of birth', 'value' => $this->dateOfBirth],
                'gender' => ['displayName' => 'Gender', 'value' => $this->gender],
                'occupation' => ['displayName' => 'Occupation', 'value' => $this->occupation],
            ]
        ];
    }
}
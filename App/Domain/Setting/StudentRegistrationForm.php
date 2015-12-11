<?php

namespace App\Domain\Setting;

use JsonSerializable;

class StudentRegistrationForm implements JsonSerializable
{
    /**
     * @return array
     */
     private static function requiredFields() {
         return [
             new FormField('firstName', 'First name', true),
             new FormField('lastName', 'Last name', true)
         ];
     }

    /**
     * @var array FormField
     */
    private $fields = [];

    /**
     * @var array FormField
     */
    private $extraFields = [];

    /**
     * @return array FormField
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * @param FormField $field
     */
    public function addField(FormField $field)
    {
        $this->fields[] = $field;
    }

    /**
     * @return array FormField
     */
    public function extraFields()
    {
        return $this->extraFields;
    }

    /**
     * @param FormField $field
     */
    public function addExtraField(FormField $field)
    {
        $this->extraFields[] = $field;
    }

    /**
     * @return string
     */
    public static function defaultJson()
    {
        return json_encode([
            'requiredFields' => self::requiredFields(),
            'fields' => [
                new FormField('gender', 'Gender', true),
                new FormField('occupation', 'Occupation', true),
                new FormField('dateOfBirth', 'Date of birth', true),
                new FormField('address', 'Address', true)
            ],
            'extraFields' => []
        ]);
    }

    /**
     * @param $json
     * @return StudentRegistrationForm
     */
    public static function makeFromJson($json)
    {
        $form = new StudentRegistrationForm();
        $decoded = json_decode($json, true);

        foreach ($decoded as $fieldType => $fields)
        {
            switch ($fieldType )
            {
                case 'fields':
                    foreach ($fields as $field)
                    {
                        $form->addField(new FormField($field['name'], $field['displayName'], $field['active']));
                    }
                    break;
                case 'extraFields':
                    foreach ($fields as $field)
                    {
                        $form->addExtraField(new FormField($field['name'], $field['displayName'], $field['active']));
                    }
                    break;
            }
        }

        return $form;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'requiredFields' => self::requiredFields(),
            'fields' => $this->fields,
            'extraFields' => $this->extraFields
        ];
    }
}
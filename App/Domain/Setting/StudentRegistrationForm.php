<?php

namespace App\Domain\Setting;

use JsonSerializable;

class StudentRegistrationForm implements JsonSerializable
{
    /**
     * @var array FormField
     */
    private $fields = [];

    /**
     * @var array FormField
     */
    private $extraFields = [];

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->makeFromJson($data);
    }

    public function requiredFields()
    {
        return [
            new FormField('firstName', 'First name', true),
            new FormField('lastName', 'Last name', true)
        ];
    }

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

    public function hasErrors()
    {
        return sizeof($this->errors) > 0;
    }

    public function errors()
    {
        return $this->errors;
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
     * @param $data
     * @return StudentRegistrationForm
     * @internal param $json
     */
    private function makeFromJson($data)
    {
        if (sizeof($data) == 0)
        {
            return;
        }

        if (!$this->validate($data))
        {
            return;
        }

        foreach ($data['fields'] as $field)
        {
            $this->addField(new FormField($field['name'], $field['displayName'], $field['active']));
        }

        foreach ($data['extraFields'] as $field)
        {
            if ($field['displayName'] == '')
            {
                continue;
            }

            if ($field['name'] == '')
            {
                $field['name'] = preg_replace('\'/\s+/\'', '', $field['displayName']);
            }

            $this->addExtraField(new FormField($field['name'], $field['displayName'], $field['active']));
        }
    }

    private function validate($data)
    {
        if (!array_key_exists('fields', $data))
        {
            $this->errors[] = 'Fields are missing.';
            return false;
        }

        if (!array_key_exists('extraFields', $data))
        {
            $this->errors[] = 'Extra fields are missing.';
            return false;
        }

        foreach ($data['fields'] as $field)
        {
            if (!isset($field['name']))
            {
                $this->errors[] = 'Field name is missing or empty.';
                return false;
            }

            if (!isset($field['displayName']))
            {
                $this->errors[] = 'Display name for '.$field['name'].' is missing or empty.';
                return false;
            }

            if (!isset($field['active']))
            {
                $this->errors[] = 'Active value for '.$field['name'].' is missing or empty.';
                return false;
            }
        }

        foreach ($data['extraFields'] as $field)
        {
            if (!array_key_exists('name', $field))
            {
                $this->errors[] = 'Extra field name is missing';
                return false;
            }

            if (!array_key_exists('displayName', $field))
            {
                $this->errors[] = 'Display name for '.$field['name'].' is missing.';
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'requiredFields' => $this->requiredFields(),
            'fields' => $this->fields,
            'extraFields' => $this->extraFields
        ];
    }
}

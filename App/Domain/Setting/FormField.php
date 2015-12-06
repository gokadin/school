<?php

namespace App\Domain\Setting;

use Library\DataMapper\DataMapperPrimaryKey;
use JsonSerializable;

/**
 * @Entity(name="form_field")
 */
class FormField implements JsonSerializable
{
    use DataMapperPrimaryKey;

    /** @Column(type="string") */
    private $name;

    /** @Column(type="string") */
    private $displayName;

    /** @BelongsTo(target="App\Domain\Setting\StudentRegistrationForm") */
    private $form;

    public function __construct(StudentRegistrationForm $form, $name, $displayName)
    {
        $this->form = $form;
        $this->name = $name;
        $this->displayName = $displayName;
    }

    public function name()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function displayName()
    {
        return $this->displayName;
    }

    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    public function form()
    {
        return $this->form;
    }

    public function setForm($form)
    {
        $this->form = $form;
    }

    public static function generateName($string)
    {
        return preg_replace('/\s+/', '', $string);
    }

    public function jsonSerialize()
    {
        return [
            'displayName' => $this->displayName,
        ];
    }
}
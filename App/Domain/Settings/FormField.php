<?php

namespace App\Domain\Settings;

use Library\DataMapper\DataMapperPrimaryKey;

/**
 * @Entity(name="form_field")
 */
class FormField
{
    use DataMapperPrimaryKey;

    /** @Column(type="string") */
    private $name;

    /** @Column(type="string") */
    private $displayName;

    /** @Column(type="string") */
    private $type;

    /** @BelongsTo(target="App\Domain\Settings\StudentRegistrationForm") */
    private $form;

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

    public function type()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function form()
    {
        return $this->form;
    }

    public function setForm($form)
    {
        $this->form = $form;
    }
}
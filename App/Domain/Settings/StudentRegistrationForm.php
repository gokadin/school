<?php

namespace App\Domain\Settings;

use Library\DataMapper\Collection\EntityCollection;
use Library\DataMapper\DataMapperPrimaryKey;
use Library\DataMapper\DataMapperTimestamps;

/**
 * @Entity(name="student_registration_form")
 */
class StudentRegistrationForm
{
    use DataMapperPrimaryKey, DataMapperTimestamps;

    /** @BelongsTo(target="App\Domain\Users\Teacher") */
    private $teacher;

    /** @HasMany(target="App\Domain\Settings\FormField", mappedBy="form") */
    private $fields;

    public function __construct($teacher)
    {
        $this->teacher = $teacher;
        $this->fields = new EntityCollection();
    }

    public function teacher()
    {
        return $this->teacher;
    }

    public function setTeacher($teacher)
    {
        $this->teacher = $teacher;
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
}
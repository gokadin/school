<?php

namespace App\Domain\Setting;

/**
 * @Entity(name="teacher_settings")
 */
class TeacherSettings extends UserSettings
{
    /** @Column(type="text") */
    private $registrationForm;

    public function __construct($registrationForm)
    {
        parent::__construct();

        $this->registrationForm = $registrationForm;
    }

    public function registrationForm()
    {
        return $this->registrationForm;
    }

    public function setRegistrationForm($registrationForm)
    {
        $this->registrationForm = $registrationForm;
    }
}
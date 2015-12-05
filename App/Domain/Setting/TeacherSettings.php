<?php

namespace App\Domain\Setting;

use Library\DataMapper\DataMapperPrimaryKey;
use Library\DataMapper\DataMapperTimestamps;

/**
 * @Entity(name="teacher_settings")
 */
class TeacherSettings
{
    use DataMapperPrimaryKey, DataMapperTimestamps;

    /** @HasOne(target="App\Domain\Setting\StudentRegistrationForm") */
    private $registrationForm;

    /** @Column(type="boolean", default="1") */
    private $showTips;

    public function __construct($registrationForm)
    {
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

    public function showTips()
    {
        return $this->showTips;
    }

    public function setShowTips($showTips)
    {
        $this->showTips = $showTips;
    }
}
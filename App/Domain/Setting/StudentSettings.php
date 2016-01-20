<?php

namespace App\Domain\Setting;

/**
 * @Entity('teacher_settings')
 */
class StudentSettings extends UserSettings
{
    public function __construct()
    {
        parent::__construct();
    }
}
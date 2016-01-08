<?php

namespace App\Domain\Services;

use App\Domain\Setting\StudentRegistrationForm;
use App\Domain\Users\Teacher;

class SettingService extends AuthenticatedService
{
    public function updatePreferencea(array $data)
    {
        $settings = $this->user->settings();

        $settings->setShowTips(isset($data['showTips']));

        $this->repository->of(Teacher::class)->update($this->user);

        return true;
    }

    public function getRegistrationForm()
    {
        return $this->user->settings()->registrationForm();
    }

    public function updateRegistrationForm(array $data)
    {
        $form = new StudentRegistrationForm($data['form']);

        if ($form->hasErrors())
        {
            return false;
        }

        $this->user->settings()->setRegistrationForm(json_encode($form));

        $this->repository->of(Teacher::class)->update($this->user);

        return $form->extraFields();
    }
}
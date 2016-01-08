<?php

namespace App\Domain\Services;

use App\Domain\Users\Teacher;

class SettingService extends AuthenticatedService
{
    public function updatePreferencea(array $data)
    {
        $settings = $this->repository->of(Teacher::class)->settingsOf($this->user);
/// stopped here...
        return true;
    }

    public function getRegistrationForm()
    {
        return $userRepository->getLoggedInUser()->settings()->registrationForm();
    }

    public function updateRegistrationForm(array $data)
    {
        $form = new StudentRegistrationForm($request->form);

        if ($form->hasErrors())
        {
            return $this->respondBadRequest(['errors' => [
                $form->getErrors()
            ]]);;
        }

        $userRepository->getLoggedInUser()->settings()->setRegistrationForm(json_encode($form));
        $dm->flush();
    }
}
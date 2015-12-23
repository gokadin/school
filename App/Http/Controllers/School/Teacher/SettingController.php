<?php

namespace App\Http\Controllers\School\Teacher;

use App\Domain\Services\SettingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\UpdatePreferencesRequest;

class SettingController extends Controller
{
    public function schoolInformation()
    {
        return $this->view->make('school.teacher.setting.schoolInformation');
    }

    public function registrationForm()
    {
        return $this->view->make('school.teacher.setting.registrationForm');
    }

    public function preferences()
    {
        return $this->view->make('school.teacher.setting.preferences');
    }

    public function updatePreferences(UpdatePreferencesRequest $request, SettingService $settingService)
    {
        $settingService->updatePreferencea($request->all())
            ? $this->session->setFlash('Preferences updated!')
            : $this->session->setFlash('Could not update preferences. Please try again.', 'error');

        $this->response->route('school.teacher.setting.preferences');
    }
}
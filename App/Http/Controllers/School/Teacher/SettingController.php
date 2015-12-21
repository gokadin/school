<?php

namespace App\Http\Controllers\School\Teacher;

use App\Domain\Services\SettingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\UpdatePreferencesRequest;
use Library\Http\Response;
use Library\Http\View;
use Library\Session\Session;

class SettingController extends Controller
{
    /**
     * @var SettingService
     */
    private $settingService;

    public function __construct(View $view, Session $session, Response $response, SettingService $settingService)
    {
        parent::__construct($view, $session, $response);

        $this->settingService = $settingService;
    }


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

    public function updatePreferences(UpdatePreferencesRequest $request)
    {
        $this->settingService->updatePreferencea($request->all())
            ? $this->session->setFlash('Preferences updated!')
            : $this->session->setFlash('Could not update preferences. Please try again.', 'error');

        $this->response->route('school.teacher.setting.preferences');
    }
}
<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Domain\Services\SettingService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\School\UpdateRegistrationFormRequest;
use App\Repositories\UserRepository;

class SettingController extends ApiController
{
    public function getRegistration(SettingService $settingService)
    {
        return $settingService->getRegistrationForm();
    }

    public function updateRegistrationForm(UpdateRegistrationFormRequest $request, SettingService $settingService)
    {
        $extraFields = $settingService->updateRegistrationForm($request->all());

        return $this->respondOk(['extraFields' => $extraFields]);
    }
}
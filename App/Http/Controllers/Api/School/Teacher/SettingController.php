<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Domain\Setting\StudentRegistrationForm;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\School\UpdateRegistrationFormRequest;
use App\Repositories\UserRepository;
use Library\DataMapper\DataMapper;

class SettingController extends ApiController
{
    public function getRegistration(UserRepository $userRepository)
    {
        return $userRepository->getLoggedInUser()->settings()->registrationForm();
    }

    public function updateRegistrationForm(UpdateRegistrationFormRequest $request,
                                           UserRepository $userRepository, DataMapper $dm)
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

        return $this->respondOk([
            'extraFields' => $form->extraFields()
        ]);
    }
}
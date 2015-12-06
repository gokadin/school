<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Domain\Setting\FormField;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\School\UpdateRegistrationFormRequest;
use App\Repositories\UserRepository;
use Library\DataMapper\DataMapper;

class SettingController extends Controller
{
    public function getRegistration(UserRepository $userRepository)
    {
        return $userRepository->getLoggedInUser()->settings()->registrationForm();
    }

    public function updateRegistrationForm(UpdateRegistrationFormRequest $request,
                                           UserRepository $userRepository, DataMapper $dm)
    {
        $form = $userRepository->getLoggedInUser()->settings()->registrationForm();

        foreach ($request->regularFields as $name => $data)
        {
            $form->setField($name, $data['value']);
        }

        $displayNames = [];
        foreach ($request->extraFields as $data)
        {
            if ($data['displayName'] == '')
            {
                continue;
            }

            $displayNames[] = $data['displayName'];
        }

        $currentDisplayNames = [];
        foreach ($form->fields() as $field)
        {
            if (!in_array($field->displayName(), $displayNames))
            {
                $dm->delete($field);
                $form->removeField($field);

                continue;
            }

            $currentDisplayNames[] = $field->displayName();
        }

        foreach ($displayNames as $displayName)
        {
            if (!in_array($displayName, $currentDisplayNames))
            {
                $field = new FormField($form, FormField::generateName($displayName), $displayName);
                $dm->persist($field);
                $form->addField($field);
            }
        }

        $dm->flush();

        return true;
    }
}
<?php

namespace App\Repositories;

class SettingRepository extends AuthenticatedRepository
{
    public function updatePreferences(array $data)
    {
        $settings = $this->user->settings();

        $settings->setShowTips(isset($data['showTips']));

        $this->dm->flush();
    }
}
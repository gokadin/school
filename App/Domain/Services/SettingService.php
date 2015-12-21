<?php

namespace App\Domain\Services;

use App\Repositories\SettingRepository;

class SettingService extends LoginService
{
    /**
     * @var SettingRepository
     */
    private $settingRepository;

    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    public function updatePreferencea(array $data)
    {
        $this->settingRepository->updatePreferences($data);

        return true;
    }
}
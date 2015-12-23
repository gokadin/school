<?php

namespace App\Domain\Services;

use App\Repositories\SettingRepository;
use Library\Events\EventManager;
use Library\Queue\Queue;
use Library\Transformer\Transformer;

class SettingService extends Service
{
    /**
     * @var SettingRepository
     */
    private $settingRepository;

    public function __construct(Queue $queue, EventManager $eventManager, Transformer $transformer,
                                SettingRepository $settingRepository)
    {
        parent::__construct($queue, $eventManager, $transformer);

        $this->settingRepository = $settingRepository;
    }

    public function updatePreferencea(array $data)
    {
        $this->settingRepository->updatePreferences($data);

        return true;
    }
}
<?php

namespace App\Domain\Services;

use App\Domain\Transformers\StudentTransformer;
use App\Repositories\ActivityRepository;
use App\Repositories\UserRepository;
use Library\Events\EventManager;
use Library\Queue\Queue;

class ActivityService extends LoginService
{
    /**
     * @var ActivityRepository
     */
    private $activityRepository;

    /**
     * @var StudentTransformer
     */
    private $studentTransformer;

    public function __construct(Queue $queue, EventManager $eventManager, UserRepository $userRepository,
                                ActivityRepository $activityRepository, StudentTransformer $studentTransformer)
    {
        parent::__construct($queue, $eventManager, $userRepository);

        $this->activityRepository = $activityRepository;
        $this->studentTransformer = $studentTransformer;
    }

    public function getActivityList(array $data)
    {
        $sortingRules = isset($data['sortingRules']) ? $data['sortingRules'] : [];
        $searchRules = isset($data['searchRules']) ? $data['searchRules'] : [];

        return $this->activityRepository->paginate(
            $data['page'], $data['max'] > 20 ? 20 : $data['max'], $sortingRules, $searchRules);
    }

    public function getActivityStudentList($id)
    {
        $activity = $this->activityRepository->find($id);
        if (is_null($activity))
        {
            return false;
        }

        return $this->studentTransformer->only(['id', 'fullName'])
            ->transformCollection($activity->students()->toArray());
    }

    public function create(array $data)
    {
        $data['teacher'] = $this->user();

        $this->activityRepository->create($data);

        return true;
    }
}
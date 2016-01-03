<?php

namespace App\Domain\Services;

use App\Domain\Activities\Activity;
use App\Domain\Users\Student;
use App\Repositories\ActivityRepository;
use App\Repositories\UserRepository;
use Library\Events\EventManager;
use Library\Queue\Queue;
use Library\Transformer\Transformer;

class ActivityService extends AuthenticatedService
{
    /**
     * @var ActivityRepository
     */
    private $activityRepository;

    public function __construct(Queue $queue, EventManager $eventManager, Transformer $transformer,
                                UserRepository $userRepository, ActivityRepository $activityRepository)
    {
        parent::__construct($queue, $eventManager, $transformer, $userRepository);

        $this->activityRepository = $activityRepository;
    }

    public function getActivities()
    {
        return $this->transformer->of(Activity::class)->transform($this->user->activities()->toArray());
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

        return $this->transformer->of(Student::class)->only(['id', 'fullName'])
            ->transform($activity->students()->toArray());
    }

    public function create(array $data)
    {
        $data['teacher'] = $this->user();

        $this->activityRepository->create($data);

        return true;
    }

    public function delete($id)
    {
        $activity = $this->user->activities()->find($id);
        if (is_null($activity))
        {
            return false;
        }

        $this->activityRepository->delete($activity);

        return true;
    }

    public function update($data, $id)
    {
        $activity = $this->user->activities()->find($id);
        if (is_null($activity))
        {
            return false;
        }

        $this->activityRepository->update($activity, $data);

        return true;
    }
}
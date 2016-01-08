<?php

namespace App\Domain\Services;

use App\Domain\Activities\Activity;
use App\Domain\Users\Student;

class ActivityService extends AuthenticatedService
{
    public function getActivities()
    {
        return $this->transformer->of(Activity::class)
            ->transform($this->user->activities()->toArray());
    }

    public function getActivityList(array $data)
    {
        $sortingRules = isset($data['sortingRules']) ? $data['sortingRules'] : [];
        $searchRules = isset($data['searchRules']) ? $data['searchRules'] : [];

        return $this->repository->paginate($this->user->activities(),
            $data['page'], $data['max'] > 20 ? 20 : $data['max'], $sortingRules, $searchRules);
    }

    public function getActivityStudentList($id)
    {
        $activity = $this->repository->of(Activity::class)->find($id);
        if (is_null($activity))
        {
            return false;
        }

        return $this->transformer->of(Student::class)->only(['id', 'fullName'])
            ->transform($activity->students()->toArray());
    }

    public function create(array $data)
    {
        $data['teacher'] = $this->user;

        $this->repository->of(Activity::class)->create($data);

        return true;
    }

    public function delete($id)
    {
        $activity = $this->user->activities()->find($id);
        if (is_null($activity))
        {
            return false;
        }

        $this->repository->of(Activity::class)->delete($activity);

        return true;
    }

    public function update($data, $id)
    {
        $activity = $this->user->activities()->find($id);
        if (is_null($activity))
        {
            return false;
        }

        $activity->setName($data['name']);
        $activity->setRate($data['rate']);
        $activity->setPeriod($data['period']);

        $this->repository->of(Activity::class)->update($activity);

        return true;
    }
}
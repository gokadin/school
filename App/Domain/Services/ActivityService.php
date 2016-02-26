<?php

namespace App\Domain\Services;

use App\Domain\Activities\Activity;
use App\Domain\Users\Student;
use App\Domain\Users\Teacher;

class ActivityService extends AuthenticatedService
{
    public function getActivities()
    {
        return $this->transformer->of(Activity::class)
            ->transform($this->user->activities()->toArray());
    }

    public function paginate(Teacher $teacher, int $page, int $max, array $sortingRules, array $searchRules): array
    {
        return $this->repository->paginate(
            $teacher->activities(), $page, $max > 20 ? 20 : $max, $sortingRules, $searchRules);
    }

    public function studentList(Teacher $teacher, int $activityId): array
    {
        $activity = $teacher->activities()->find($activityId);

        return is_null($activity) ? [] : $activity->students()->toArray();
    }

    public function create(array $data)
    {
        $data['teacher'] = $this->user;

        return !is_null($this->repository->of(Activity::class)->create($data));
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
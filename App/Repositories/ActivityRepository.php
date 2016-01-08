<?php

namespace App\Repositories;

use App\Domain\Activities\Activity;

class ActivityRepository extends RepositoryBase
{
    public function create(array $data)
    {
        $activity = new Activity($data['teacher'], $data['name'], $data['rate'], $data['period']);

        if (isset($data['location']))
        {
            $activity->setLocation($data['location']);
        }

        $this->dm->persist($activity);

        $this->dm->flush();

        return $activity;
    }
}
<?php

namespace App\Repositories;

use App\Domain\Activities\Activity;

class ActivityRepository extends Repository
{
    public function create($data)
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
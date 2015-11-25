<?php

namespace App\Repositories;

use App\Domain\Activities\Activity;
use PDOException;

class ActivityRepository extends Repository
{
    public function create($data)
    {
        $activity = new Activity($data['teacher'], $data['name'], $data['rate'], $data['period']);

        if (isset($data['location']))
        {
            $activity->setLocation($data['location']);
        }

        try
        {
            $this->dm->persist($activity);
            return $activity;
        }
        catch (PDOException $e)
        {
            $this->log->error('ActivityRepository.create : could not create activity : '.$e->getMessage());
            return false;
        }
    }
}
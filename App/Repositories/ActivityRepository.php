<?php

namespace App\Repositories;

use App\Domain\Activities\Activity;
use App\Domain\Transformers\ActivityTransformer;
use Library\DataMapper\DataMapper;
use Library\Log\Log;

class ActivityRepository extends AuthenticatedRepository
{
    protected $transformer;

    public function __construct(DataMapper $dm, Log $log, UserRepository $userRepository, ActivityTransformer $transformer)
    {
        parent::__construct($dm, $log, $userRepository);

        $this->transformer = $transformer;
    }

    public function paginate($pageNumber, $pageCount, array $sortingRules = [], array $searchRules = [])
    {
        $activities = $this->user->activities();

        $result = $this->paginateCollection($activities, $pageNumber, $pageCount, $sortingRules, $searchRules);

        return [
            'data' => $this->transformer->transformCollection($result['data']),
            'pagination' => $result['pagination']
        ];
    }

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

    public function delete(Activity $activity)
    {
        $this->dm->delete($activity);
        $this->dm->flush();
    }

    public function update(Activity $activity, array $data)
    {
        $activity->setName($data['name']);
        $activity->setRate($data['rate']);
        $activity->setPeriod($data['period']);

        $this->dm->flush();
    }
}
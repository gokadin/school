<?php

namespace App\Repositories;

use App\Domain\Activities\Activity;
use Library\DataMapper\DataMapper;
use Library\Log\Log;
use Library\Transformer\Transformer;

class ActivityRepository extends AuthenticatedRepository
{
    protected $transformer;

    public function __construct(DataMapper $dm, Log $log, UserRepository $userRepository, Transformer $transformer)
    {
        parent::__construct($dm, $log, $userRepository);

        $this->transformer = $transformer;
    }

    public function find($id)
    {
        return $this->dm->find(Activity::class, $id);
    }

    public function paginate($pageNumber, $pageCount, array $sortingRules = [], array $searchRules = [])
    {
        $activities = $this->user->activities();

        $result = $this->paginateCollection($activities, $pageNumber, $pageCount, $sortingRules, $searchRules);

        return array(
            'data' => $this->transformer->of(Activity::class)->transform($result['data']),
            'pagination' => $result['pagination']
        );
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
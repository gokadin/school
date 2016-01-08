<?php

namespace App\Repositories;

use Library\DataMapper\Collection\PersistentCollection;
use Library\DataMapper\DataMapper;
use Library\Log\Log;

class Repository
{
    /**
     * @var DataMapper
     */
    private $dm;

    /**
     * @var Log
     */
    private $log;

    /**
     * @var array
     */
    private $repositories = [];

    public function __construct(DataMapper $dm, Log $log)
    {
        $this->dm = $dm;
        $this->log = $log;
    }

    public function of($class)
    {
        $shortName = substr($class, strrpos($class, '\\') + 1);
        $repositoryClass = '\\App\\Repositories\\'.$shortName.'Repository';

        return isset($this->repositories[$class])
            ? $this->repositories[$class]
            : $this->initializeRepository($repositoryClass, $class);
    }

    private function initializeRepository($repositoryClass, $class)
    {
        return $this->repositories[$class] = new $repositoryClass($this->dm, $this->log, $class);
    }

    public function paginate(PersistentCollection $collection, $pageNumber, $pageCount, array $sortingRules, array $searchRules)
    {
        foreach ($sortingRules as $property => $ascending)
        {
            $collection->sortBy($property, $ascending == 'asc');
        }

        foreach ($searchRules as $property => $string)
        {
            $collection->where($property, 'LIKE', '%'.$string.'%');
        }

        return [
            'data' => $collection->slice($pageNumber * $pageCount, $pageCount),
            'pagination' => [
                'totalCount' => $collection->count(),
                'pageNumber' => $pageNumber,
                'pageCount' => $pageCount
            ]
        ];
    }
}
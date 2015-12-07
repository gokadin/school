<?php

namespace App\Repositories;

use Library\DataMapper\Collection\PersistentCollection;
use Library\DataMapper\DataMapper;
use Library\Log\Log;

class Repository
{
    protected $dm;
    protected $log;

    public function __construct(DataMapper $dm, Log $log)
    {
        $this->dm = $dm;
        $this->log = $log;
    }

    protected function paginateCollection(PersistentCollection $collection, $pageNumber, $pageCount, array $sortingRules, array $searchRules)
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
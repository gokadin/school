<?php namespace Library\Database;

class Query
{
    protected $dao;

    public function __construct($dao)
    {
        $this->dao = $dao;
    }
}
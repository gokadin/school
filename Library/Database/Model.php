<?php namespace Library\Database;

use Library\Facades\DB;

class Model
{
    protected $dao;
    protected $blueprint;

    public function __construct()
    {
        $this->dao = DB::dao();

        $modelName = get_called_class();
        $modelName = strtolower(substr(get_called_class(), strrpos($modelName, '\\') + 1));
        $this->blueprint = DB::getBlueprint($modelName);
    }
}

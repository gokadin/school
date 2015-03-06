<?php namespace Library\Database;

use Library\Facades\DB;

class Model
{
    protected $dao;
    protected $blueprint;
    protected $vars = array();

    public function __construct(array $data = array())
    {
        $this->dao = DB::dao();

        $modelName = get_called_class();
        $modelName = strtolower(substr(get_called_class(), strrpos($modelName, '\\') + 1));
        $this->blueprint = DB::getBlueprint($modelName);

        if (!empty($data)) {
            foreach ($data as $var => $value) {
                $this->__set($var, $value);
            }
        }
    }

    public function __set($var, $value) {
        $this->vars[$var] = $value;
    }

    public function __get($var) {
        if (isset($this->vars[$var]))
            return $this->vars[$var];
    }

    /* MOVE METHODS BELOW OUT OF HERE */

    public function save()
    {
        // if is new blablabla
        // make a common query interface
        // which is also accessible from db class
    }
}

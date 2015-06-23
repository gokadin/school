<?php namespace Library;

use Symfony\Component\Yaml\Exception\RuntimeException;

abstract class BackController
{
    protected $vars = array();
    protected $lang = null;
	
    public function add($var, $value = null)
    {
        if ($value != null)
        {
            if (!is_string($var))
                return;

            $this->vars[$var] = $value;
            return;
        }

        if (!is_array($var))
            return;

        foreach ($var as $key => $v)
            $this->vars[$key] = $v;
    }

    public function __get($var)
    {
        if (isset($this->vars[$var]))
            return $this->vars[$var];
    }

    public function execute()
    {
        $action = \Library\Facades\App::action();
        
        if (!is_callable(array($this, $action)))
            throw new \RuntimeException('The action '.$action.' is not defined on this module');

        $this->$action();
    }
    
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    protected function validateToken()
    {
        if (!\Library\Facades\Request::dataExists('_token') || \Library\Facades\Request::data('_token') != \Library\Facades\Session::generateToken())
            throw new RuntimeException('CSRF token mismatch.');
    }

    protected function validateRequest(array $rules)
    {
        if ($rules == null || sizeof($rules) == 0)
            return;

        foreach ($rules as $rule)
        {
            // ...
        }
    }
}

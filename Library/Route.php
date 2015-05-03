<?php namespace Library;

class Route
{
    protected $appName;
    protected $action;
    protected $module;
    protected $url;
    protected $method;
    protected $varsNames;
    protected $vars = array();

    public function __construct($appName, $module, $action, $url, $method, array $varsNames = null)
    {
        $this->appName = $appName;
        $this->setUrl($url);
        $this->setMethod($method);
        $this->setModule($module);
        $this->setAction($action);
        $this->setVarsNames($varsNames);
    }

    public function hasVars()
    {
        return $this->varsNames != null && !empty($this->varsNames);
    }

    public function match($appName, $url, $method)
    {   
        if (strcasecmp($method, $this->method) != 0 || strcasecmp($appName, $this->appName) != 0)
        {
            return false;
        }
        
        $substituteUrl = $this->url;
        if ($this->hasVars())
        {
            
            
            $substituteUrl = preg_replace('({[0-9]+})', '(.+)', $this->url);
        }
        
        if (preg_match('`^' . strtolower($substituteUrl) . '$`', strtolower($url), $matches) != 1)
        {
            return false;
        }

        preg_match_all('{([0-9]+)}', $this->url, $seqMatches);
        for ($i = 0; $i < sizeof($seqMatches[1]); $i++)
        {
            $varSequence[] = $seqMatches[1][$i];
        }

        for ($i = 1; $i < sizeof($matches); $i++)
        {
            $this->vars[$this->varsNames[$varSequence[$i - 1]]] = $matches[$i];
        }
        
        return true;
    }

    public function matchAction($appName, $module, $action)
    {
        return strcasecmp($appName, $this->appName) == 0 &&
            strcasecmp($module, $this->module) == 0 &&
            strcasecmp($action, $this->action) == 0;
    }

    public function resolveUrl($args)
    {
        if (!$this->hasVars())
            return $this->url;
            
        if (!is_array($args))
        {
            return preg_replace('/({[0-9]+})/', $args, $this->url);
        }

        return preg_replace_callback('/({[0-9]+})/', function($matches) use (&$args) {
            return $args[substr(substr($matches[0], 1), 0, -1)];
        }, $this->url);
    }

    public function setAction($action)
    {
        if (is_string($action))
            $this->action = $action;
    }

    public function setModule($module)
    {
        if (is_string($module))
            $this->module = $module;
    }

    public function setUrl($url)
    {
        if (is_string($url))
            $this->url = $url;
    }

    public function setMethod($method)
    {
        if (is_string($method))
            $this->method = $method;
    }

    public function setVarsNames(array $varsNames = null)
    {
        $this->varsNames = $varsNames;
    }

    public function setVars(array $vars)
    {
        $this->vars = $vars;
    }

    public function action()
    {
        return $this->action;
    }

    public function module()
    {
        return $this->module;
    }

    public function url()
    {
        return $this->url;
    }

    public function method()
    {
        return $this->method;
    }

    public function vars()
    {
        return $this->vars;
    }

    public function varsNames()
    {
        return $this->varsNames;
    }
}

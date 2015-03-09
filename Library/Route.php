<?php namespace Library;

class Route
{
    protected $action;
    protected $module;
    protected $url;
    protected $method;
    protected $varsNames;
    protected $vars = array();

    public function __construct($url, $module, $method, $action, array $varsNames) {
        $this->setUrl($url);
        $this->setMethod($method);
        $this->setModule($module);
        $this->setAction($action);
        $this->setVarsNames($varsNames);
    }

    public function hasVars() {
        return !empty($this->varsNames);
    }

    public function match($url, $method) {
        if (preg_match('`^' . $this->url . '$`', $url, $matches) && strcmp($method, $this->method) == 0) {
            return $matches;
        } else {
            return false;
        }
    }

    public function setAction($action) {
        if (is_string($action)) {
            $this->action = $action;
        }
    }

    public function setModule($module) {
        if (is_string($module)) {
            $this->module = $module;
        }
    }

    public function setUrl($url) {
        if (is_string($url)) {
            $this->url = $url;
        }
    }

    public function setMethod($method)
    {
        if (is_string($method)) {
            $this->method = $method;
        }
    }

    public function setVarsNames(array $varsNames) {
        $this->varsNames = $varsNames;
    }

    public function setVars(array $vars) {
        $this->vars = $vars;
    }

    public function action() {
        return $this->action;
    }

    public function module() {
        return $this->module;
    }

    public function method()
    {
        return $this->method;
    }

    public function vars() {
        return $this->vars;
    }

    public function varsNames() {
        return $this->varsNames;
    }
}

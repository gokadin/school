<?php namespace Library;

use Library\Shao\Shao;

abstract class BackController extends ApplicationComponent {
    protected $action = '';
    protected $module = '';
    protected $method = '';
    protected $page = null;
    protected $view = '';
    protected $lang = null;
	
    public function __construct(Application $app, $module, $method, $action) {
        parent::__construct($app);

        $this->page = new Page($app);
        $this->app()->instance('page', $this->page);
        $this->setModule($module);
        $this->setMethod($method);
        $this->setAction($action);
        $this->setView($action);
    }

    public function execute()
    {
        $function_name = $this->action;

        if (!is_callable(array($this, $function_name))) {
            throw new \RuntimeException('The action '.$this->action.' is not defined on this module');
        }

        $this->$function_name();
    }

    public function page() {
        return $this->page;
    }

    public function setModule($module)
    {
        if (!is_string($module) || empty($module)) {
            throw new \InvalidArgumentException('This module has to be a string');
        }

        $this->module = $module;
    }

    public function setMethod($method)
    {
        if (!is_string($method) || empty($method))
        {
            throw new \InvalidArgumentException('This method has to be a string');
        }

        $this->method = $method;
    }

    public function setAction($action)
    {
        if (!is_string($action) || empty($action)) {
            throw new \InvalidArgumentException('This action has to be a string');
        }

        $this->action = $action;
    }

    public function setView($view)
    {
        if (!is_string($view) || empty($view))
            throw new \InvalidArgumentException('This view has to be a string');

        $this->view = $view;

        $viewPrefix = 'Applications/' . $this->app->name() . '/Modules/' . $this->module . '/Views/';
        $contentFile = null;
        if (file_exists($viewPrefix . $this->view . '.shao.html')) // change later for in_array based on config
        {
            $contentFile = Shao::parseFile($viewPrefix . $this->view . '.shao.html');
        }
        else if (file_exists($viewPrefix . $this->view . '.php'))
        {
            $contentFile = $viewPrefix . $this->view . '.php';
        }
        else if (file_exists($viewPrefix . $this->view . '.html'))
        {
            $contentFile = $viewPrefix . $this->view . '.html';
        }

        $layoutPrefix = 'Applications/'.$this->app->name().'/Templates/';
        $layoutFile = null;
        if (file_exists($layoutPrefix.'layout.shao.html')) // change later for in_array based on configs
        {
            $layoutFile = Shao::parseFile($layoutPrefix.'layout.shao.html');
        }
        else if (file_exists($layoutPrefix.'layout.php'))
        {
            $layoutFile = $layoutPrefix.'layout.php';
        }
        else if (file_exists($layoutPrefix.'layout.html'))
        {
            $layoutFile = $layoutPrefix.'layout.html';
        }

        $this->page->setContentFile($contentFile);
        $this->page->setLayoutFile($layoutFile);

        require 'Web/lang/common.php';
        $this->setLang($lang);
        $this->page->add('lang', $lang);
    }
    
    public function module() {
        return $this->module;
    }

    public function method()
    {
        return $this->method;
    }
    
    public function action() {
        return $this->action;
    }
    
    public function setLang($lang) {
        $this->lang = $lang;
    }
}

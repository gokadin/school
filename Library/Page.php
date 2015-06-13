<?php namespace Library;

use \Library\Shao\Shao;

class Page
{
    protected $contentFile;
    protected $layoutFile;
    protected $vars = array();
    
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

    public function exists($var)
    {
        return isset($this->vars[$var]);
    }

    public function get($var)
    {
        if (isset($this->vars[$var]))
            return $this->vars[$var];
    }

    public function getGeneratedPage()
    {
        $this->processView();
        
        if (!file_exists($this->contentFile))
            throw new \RuntimeException('Content file does not exist');

        extract($this->vars);

        ob_start();

        require $this->contentFile;

        $content = ob_get_clean();

        ob_start();

        if (empty($this->layoutFile))
            $this->setLayoutFile('layout.php');

        if (file_exists($this->layoutFile))
            require $this->layoutFile;
        else
            echo $content;

        return ob_get_clean();
    }
    
    public function setContentFile($contentFile)
    {
        $this->contentFile = $contentFile;
    }
    
    public function setLayoutFile($layoutFile)
    {
        $this->layoutFile = $layoutFile;
    }
    
    public function processView()
    {
        $view = \Library\Facades\App::action();
        
        if (!is_string($view) || empty($view))
            throw new \InvalidArgumentException('This view has to be a string');

        $viewPrefix = 'Applications/' . \Library\Facades\App::name() . '/Modules/' . \Library\Facades\App::module() . '/Views/';
        $contentFile = null;
        if (file_exists($viewPrefix . $view . '.shao.html')) // change later for in_array based on config
        {
            $contentFile = Shao::parseFile($viewPrefix . $view . '.shao.html');
        }
        else if (file_exists($viewPrefix . $view . '.php'))
        {
            $contentFile = $viewPrefix . $view . '.php';
        }
        else if (file_exists($viewPrefix . $view . '.html'))
        {
            $contentFile = $viewPrefix . $view . '.html';
        }

        $layoutPrefix = 'Applications/'.\Library\Facades\App::name().'/Templates/';
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

        $this->setContentFile($contentFile);
        $this->setLayoutFile($layoutFile);
    }
}

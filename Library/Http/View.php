<?php

namespace Library\Http;

use Library\Shao\Shao;
use Symfony\Component\Yaml\Exception\RuntimeException;

class View
{
    const VIEW_FOLDER = 'Resources/Views';

    protected $basePath;
    protected $content;
    protected $vars;

    public function __construct($view, array $data = array())
    {
        $this->basePath = __DIR__.'/../../'.self::VIEW_FOLDER;
        $this->content = $this->processView($view);
        $this->vars = $data;
    }

    public function with($var, $value = null)
    {
        if ($value != null)
        {
            if (!is_string($var))
                return $this;

            $this->vars[$var] = $value;
            return $this;
        }

        if (!is_array($var))
            return $this;

        foreach ($var as $key => $v)
            $this->vars[$key] = $v;

        return $this;
    }

    public function send()
    {
        echo $this->content;
    }

    protected function processView($view)
    {
        $view = $this->basePath.'/'.str_replace('.', '/', $view);

        $contentFile = $this->getContentFile($view);

        if ($this->vars != null)
        {
            extract($this->vars);
        }

        ob_start();

        require $contentFile;

        $content = ob_get_clean();

        ob_start();

//        if (file_exists($this->layoutFile))
//            require $this->layoutFile;
//        else
            echo $content;

        return ob_get_clean();
    }

    protected function getContentFile($view)
    {
        if (file_exists($view.'.shao.php'))
        {
            return Shao::parseFile($view.'.shao.php');
        }
        else if (file_exists($view.'.shao.html'))
        {
            return Shao::parseFile($view.'.shao.html');
        }
        else if (file_exists($view.'.php'))
        {
            return $view.'.php';
        }
        else if (file_exists($view.'.html'))
        {
            return $view.'.html';
        }

        throw new RuntimeException('Specified view '.$view.' does not exist.');
    }
}
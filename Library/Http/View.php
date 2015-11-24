<?php

namespace Library\Http;

use Library\Facades\Shao;
use Library\Facades\ViewFactory as Factory;
use Symfony\Component\Yaml\Exception\RuntimeException;

class View
{
    const VIEW_FOLDER = 'resources/views';

    protected $basePath;
    protected $content;
    protected $vars;

    public function __construct()
    {
        $this->basePath = __DIR__.'/../../'.self::VIEW_FOLDER;
    }

    public function make($view, array $data = [])
    {
        $this->add($data);
        $this->content = $this->processView($view);
        return $this;
    }

    public function add(array $data)
    {
        foreach ($data as $key => $value)
        {
            $this->vars[$key] = $value;
        }
    }

    public function send()
    {
        echo $this->content;
    }

    protected function processView($view)
    {
        $view = $this->basePath.'/'.str_replace('.', '/', $view);

        $contentFile = $this->getContentFile($view);

        if (!is_null($this->vars))
        {
            extract($this->vars);
        }

        ob_start();

        require $contentFile;

        $content = ob_get_clean();

        ob_start();

        if (Factory::hasLayout())
        {
            require Factory::getLayoutFile();
        }

        return ob_get_clean();
    }

    protected function getContentFile($view)
    {
        $validExtensions = ['.php', '.html'];
        $validShaoExtensions = ['.shao.php', '.shao.html'];

        foreach ($validExtensions as $validExtension)
        {
            if (file_exists($view.$validExtension))
            {
                return $view.$validExtension;
            }
        }

        foreach ($validShaoExtensions as $validShaoExtension)
        {
            if (file_exists($view.$validShaoExtension))
            {
                return Shao::parseFile($view.$validShaoExtension);
            }
        }

        throw new RuntimeException('File '.$view.' does not exist.');
    }
}
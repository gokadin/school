<?php

namespace Library\Http;

use Library\Container\Container;
use Library\Shao\Shao;
use Symfony\Component\Yaml\Exception\RuntimeException;

class View
{
    const VIEW_FOLDER = 'resources/views';

    private $container;
    protected $basePath;
    protected $content;
    protected $vars;
    protected $shao;

    public function __construct(Container $container, Shao $shao)
    {
        $this->container = $container;
        $this->shao = $shao;
        $this->basePath = __DIR__.'/../../'.self::VIEW_FOLDER;
    }

    public function make($view, array $data = [])
    {
        $this->add($data);
        $this->processView($view);

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

        $viewFactory = new ViewFactory($this->container);

        ob_start();
        require $contentFile;
        $content = ob_get_clean();

        if ($viewFactory->hasLayout())
        {
            ob_start();
            require $viewFactory->getLayoutFile();
            $this->content = ob_get_clean();
        }
        else {
            $this->content = $content;
        }
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
                return $this->shao->parseFile($view.$validShaoExtension);
            }
        }

        throw new RuntimeException('File '.$view.' does not exist.');
    }
}
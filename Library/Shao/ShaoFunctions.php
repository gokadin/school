<?php

namespace Library\Shao;

use Library\Container\Container;
use Library\Facades\Router;
use Library\Facades\Shao as ShaoBase;
use Library\Facades\ViewFactory;
use Library\Http\View;
use Symfony\Component\Yaml\Exception\RuntimeException;

class ShaoFunctions
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function layout($str)
    {
        $requestedFile = __DIR__.'/../../'.View::VIEW_FOLDER.'/'.str_replace('.', '/', $str);

        $validExtensions = ['.php', '.html'];
        $validShaoExtensions = ['.shao.php', '.shao.html'];

        foreach ($validExtensions as $validExtension)
        {
            if (file_exists($requestedFile.$validExtension))
            {
                ViewFactory::setLayoutFile($requestedFile.$validExtension);
                return;
            }
        }

        foreach ($validShaoExtensions as $validShaoExtension)
        {
            if (file_exists($requestedFile.$validShaoExtension))
            {
                ViewFactory::setLayoutFile(ShaoBase::parseFile($requestedFile.$validShaoExtension));
                return;
            }
        }

        throw new RuntimeException('File '.$requestedFile.' does not exist.');
    }

    public function section($str)
    {
        return '<?php viewFactoryStartSection(\''.$str.'\'); ?>';
    }

    public function stop()
    {
        return '<?php viewFactoryEndSection(); ?>';
    }

    public function _yield($str)
    {
        return '<?php viewFactoryYield(\''.$str.'\'); ?>';
    }

    public function _include($str, $currentFile)
    {
        $requestedFile = View::VIEW_FOLDER.'/'.substr($currentFile, 0, strrpos($currentFile, '/')).'/'.$str;

        $validExtensions = ['.php', '.html'];
        $validShaoExtensions = ['.shao.php', '.shao.html'];

        foreach ($validExtensions as $validExtension)
        {
            if (file_exists($requestedFile.$validExtension))
            {
                return file_get_contents($requestedFile.$validExtension);
            }
        }

        foreach ($validShaoExtensions as $validShaoExtension)
        {
            if (file_exists($requestedFile.$validShaoExtension))
            {
                return file_get_contents(ShaoBase::parseFile($requestedFile.$validShaoExtension));
            }
        }

        throw new RuntimeException('File '.$requestedFile.' does not exist.');
    }

    public function inject($class, $varName = null)
    {
        $resolved = $this->container->resolve($class);
        $view = $this->container->resolveInstance('view');

        is_null($varName)
            ? $view->add([lcfirst(substr($class, strrpos($class, '\\') + 1)) => $resolved])
            : $view->add([$varName => $resolved]);
    }

    public function path($action, $var = null)
    {
        if (is_null($var))
        {
            return Router::getUri($action);
        }

        if (!is_array($var))
        {
            return Router::getUri($action, [$var]);
        }

        return Router::getUri($action, $var);
    }
}

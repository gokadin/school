<?php

namespace Library\Shao;

use Library\Facades\Router;
use Library\Facades\Shao as ShaoBase;
use Library\Facades\ViewFactory;
use Library\Http\View;
use Symfony\Component\Yaml\Exception\RuntimeException;

class ShaoFunctions
{
    public function layout($str, $currentFile)
    {
        $requestedFile = View::VIEW_FOLDER.'/'.substr($currentFile, 0, strrpos($currentFile, '/')).'/'.$str;

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

    public function inject($str)
    {
        return '<?php use '.$str.'; ?>';
    }

    public function path($action, $var = null)
    {
        $url = Router::actionToPath($action);

        if ($var != null)
            return $url.$var;

        return $url;
    }
}

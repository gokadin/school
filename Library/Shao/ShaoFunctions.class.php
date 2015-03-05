<?php namespace Library\Shao;

use Library\Facades\App;

class ShaoFunctions
{
    public static function includeView($string)
    {
        $commonViewsPath = 'Common/Views/';
        if (file_exists($commonViewsPath.$string.'.php')) // change later for in_array based on config
        {
            return '<?php require \''.$commonViewsPath.$string.'.php\' ?>';
        }
        else if (file_exists($commonViewsPath.$string.'.html'))
        {
            return '<?php require \''.$commonViewsPath.$string.'.html\' ?>';
        }
        else if (file_exists($commonViewsPath.$string.'.shao.html'))
        {
            return '<?php require \''.Shao::parseFile($commonViewsPath.$string.'.shao.html').'\' ?>';
        }

        throw new \Exception('Shao.includeView : could not find common view');
    }

    public static function path($string)
    {
        return App::router()->getUrlFromAction($string);
    }
}

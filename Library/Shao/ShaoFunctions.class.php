<?php
namespace Library\Shao;

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
        $arr = explode('/', $string);
        $app = $arr[0];
        $module = $arr[1];
        $action = $arr[2];

        $xml = new \DOMDocument;
        $xml->load(__DIR__.'/../../Config/routes.xml');

        $applications = $xml->getElementsByTagName('application');
        $routes = array();
        foreach ($applications as $application)
        {
            if ($application->getAttribute('name') == $app)
            {
                $routes = $application->getElementsByTagName('route');
                break;
            }
        }

        if ($routes == null)
        {
            throw new \Exception('Shao.path : route not found');
            return '';
        }

        foreach ($routes as $route) {
            if ($route->getAttribute('module') == $module &&
                $route->getAttribute('action') == $action)
            {
                return $route->getAttribute('url');
            }
        }

        throw new \Exception('Shao.path : route not found.');
    }
}
?>
<?php
namespace Library;

class Shao
{
    const SHAO_FOLDER = 'Cache/Shao/';
    const EMPTY_STRING = 'n/a';

    public static function parseFile($file)
    {
        $cachedFileName = self::generateCachedFileName($file);
        $str = file_get_contents($file);

        if (!self::isFileChanged($file, $str)) {
            return $cachedFileName;
        }

        self::createMetadataFile($file, $str);

        self::parseEcho($str);
        self::parseFunctions($str);

        file_put_contents($cachedFileName, $str);

        return $cachedFileName;
    }

    private static function isFileChanged($fileName, &$fileContents)
    {
        $metadataFileName = self::generateMetadataFileName($fileName);
        if (!file_exists($metadataFileName)) {
            return true;
        }

        if (crc32($fileContents) != file_get_contents($metadataFileName)) {
            return true;
        }

        return false;
    }

    private static function createMetadataFile($fileName, &$fileContents)
    {
        $name = self::generateMetadataFileName($fileName);
        $checksum = crc32($fileContents);
        file_put_contents($name, $checksum);
    }

    private static function generateCachedFileName($file)
    {
        return self::SHAO_FOLDER . str_replace('/', '-', $file);
    }

    private static function generateMetadataFileName($file)
    {
        $str = self::generateCachedFileName($file);
        return strstr($str, '.', true) . '.metadata';
    }

    public static function clearCache()
    {
        $files = glob('Cache/Shao/*');
        foreach($files as $file)
        {
            if(is_file($file))
                unlink($file);
        }
    }

    /* PARSING FUNCTIONS */

    private static function parseEcho(&$str)
    {
        $str = str_replace('{{', '<?php echo', $str);
        $str = str_replace('}}', '; ?>', $str);
    }

    private static function parseFunctions(&$str)
    {
        $functionRegex = '/@.+\(.+\)/';

        $str = preg_replace_callback($functionRegex, 'self::parseFunctionReplaceCallback', $str);
    }

    private static function parseFunctionReplaceCallback($string)
    {
        $string = $string[0];
        $string = trim($string);
        $functionName = strstr(str_replace('@', 'self::', $string), '(', true);
        $functionArgs = substr(substr(strstr($string, '('), 0, -1), 1);
        $functionArgs = self::removeSingleQuotes($functionArgs);
        $functionArgs = explode(',', $functionArgs);

        // Exception function names:
        if ($functionName == 'self::include')
            $functionName = 'self::includeView';

        $returnValue = call_user_func_array($functionName, $functionArgs);
        if (!is_string($returnValue) || empty($returnValue))
            $returnValue = self::EMPTY_STRING;

        return $returnValue;
    }

    private static function removeSingleQuotes($string)
    {
        return substr(substr($string, 0, -1), 1);
    }

    /* ACTUAL FUNCTIONS */

    private static function path($string)
    {
        $arr = explode('/', $string);
        $app = $arr[0];
        $module = $arr[1];
        $action = $arr[2];

        $xml = new \DOMDocument;
        $xml->load(__DIR__.'/../Config/routes.xml');

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

    private static function includeView($string)
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
            return '<?php require \''.self::parseFile($commonViewsPath.$string.'.shao.html').'\' ?>';
        }

        throw new \Exception('Shao.includeView : could not find common view');
    }
}
?>
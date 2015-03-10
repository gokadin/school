<?php namespace Library\Shao;

use Library\Facades\Config;

class Shao
{
    const SHAO_FOLDER = 'Cache/Shao/';
    const FUNCTIONS_CLASS_NAME = '\Library\Shao\ShaoFunctions';
    const LOGIC_CLASS_NAME = '\Library\Shao\ShaoLogic';
    const EMPTY_STRING = 'n/a';

    public static function parseFile($file)
    {
        $cachedFileName = self::generateCachedFileName($file);
        $str = file_get_contents($file);

        if (Config::get('env') != 'local')
        {
            if (!self::isFileChanged($file, $str))
                return $cachedFileName;
        }

        self::createMetadataFile($file, $str);

        self::parseRawPhp($str);
        self::parseEchoAndEscape($str);
        self::parseEcho($str);
        self::parseLogic($str);
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

    private static function parseEchoAndEscape(&$str)
    {
        $str = str_replace('{!!', '<?php echo htmlspecialchars(', $str);
        $str = str_replace('!!}', '); ?>', $str);
    }

    private static function parseRawPhp(&$str)
    {
        $str = str_replace('{{{', '<?php ', $str);
        $str = str_replace('}}}', ' ?>', $str);
    }

    private static function parseEcho(&$str)
    {
        $str = str_replace('{{', '<?php echo ', $str);
        $str = str_replace('}}', '; ?>', $str);
    }

    private static function parseLogic(&$str)
    {
        $logicString = '';
        $bracketCounter = 0;
        $found = false;
        $nameIsBuilt = false;
        $canStopLooking = false;
        $foundStrings = array();
        for ($i = 0; $i < strlen($str); $i++)
        {
            if ($str[$i] == '@' && !$found)
            {
                if ($i < strlen($str) - 1 && !ctype_alpha($str[$i + 1])) continue;

                $found = true;
                $nameIsBuilt = false;
                $canStopLooking = false;
                $logicString = $str[$i];
                continue;
            }

            if (!$found) continue;

            if (!$nameIsBuilt && ctype_space($str[$i]))
                $nameIsBuilt = true;

            if (!$canStopLooking && $i == strlen($str) - 1)
            {
                $logicString .= $str[$i];
                $foundStrings[] = $logicString;
                continue;
            }

            if (!$canStopLooking && $nameIsBuilt && $str[$i] != ' ' && $str[$i] != '(')
            {
                $foundStrings[] = $logicString;
                $found = false;
                $canStopLooking = true;
                $bracketCounter = 0;
                $nameIsBuilt = false;
                continue;
            }

            if ($str[$i] == '(')
            {
                $bracketCounter++;
                $canStopLooking = true;
            }
            else if ($str[$i] == ')')
                $bracketCounter--;

            $logicString .= $str[$i];

            if ($canStopLooking && $bracketCounter == 0)
            {
                $foundStrings[] = $logicString;
                $found = false;
            }
        }

        foreach ($foundStrings as $foundString)
        {
            if (!strpos($foundString, '('))
            {
                $logicName = trim(substr($foundString, 1));
                $logicBody = null;
            }
            else
            {
                $logicName = trim(substr(strstr($foundString, '(', true), 1));
                $logicBody = substr(substr(strstr($foundString, '('), 1), 0, -1);
            }

            $functionName = call_user_func(self::LOGIC_CLASS_NAME.'::convertToFunctionName', $logicName);
            if (!$functionName) continue;

            if ($logicBody == null)
                $result = call_user_func(self::LOGIC_CLASS_NAME.'::'.$functionName);
            else
                $result = call_user_func(self::LOGIC_CLASS_NAME.'::'.$functionName, $logicBody);

            $str = str_replace($foundString, $result, $str);
        }
    }

    private static function parseFunctions(&$str)
    {
        $functionRegex = '/@[a-z0-9]+\(\'.+\'\)/';

        $str = preg_replace_callback($functionRegex, 'self::parseFunctionReplaceCallback', $str);
    }

    private static function parseFunctionReplaceCallback($string)
    {
        $string = $string[0];
        $string = trim($string);
        $functionName = trim(strstr(str_replace('@', self::FUNCTIONS_CLASS_NAME.'::', $string), '(', true));
        $functionArgs = substr(substr(strstr($string, '('), 0, -1), 1);
        $functionArgs = explode(',', $functionArgs);
        foreach ($functionArgs as &$functionArg) {
            $functionArg = trim($functionArg);

            if (substr($functionArg, 0, 1) == '\'' && substr($functionArg, -1) == '\'')
                $functionArg = self::removeSingleQuotes($functionArg);
        }

        // Different function names (exceptions):
        if ($functionName == self::FUNCTIONS_CLASS_NAME.'::include')
            $functionName = self::FUNCTIONS_CLASS_NAME.'::includeView';

        $classFunctions = get_class_methods(self::FUNCTIONS_CLASS_NAME);
        foreach($classFunctions as $key => $classFunction)
            $classFunctions[$key] = self::FUNCTIONS_CLASS_NAME.'::'.$classFunction;

        if (!in_array($functionName, $classFunctions))
            return $string;

        $returnValue = call_user_func_array($functionName, $functionArgs);
        if (!is_string($returnValue) || empty($returnValue))
            $returnValue = self::EMPTY_STRING;

        return $returnValue;
    }

    private static function removeSingleQuotes($string)
    {
        return substr(substr($string, 0, -1), 1);
    }
}

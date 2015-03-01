<?php
namespace Library;

class Shao
{
    const SHAO_FOLDER = 'Cache/Shao/';

    public static function parseFile($file)
    {
        $cachedFileName = self::generateCachedFileName($file);
        $str = file_get_contents($file);

        if (!self::isFileChanged($file, $str))
        {
            return $cachedFileName;
        }

        self::createMetadataFile($file, $str);

        self::parseEcho($str);

        file_put_contents($cachedFileName, $str);

        return $cachedFileName;
    }

    private static function isFileChanged($fileName, &$fileContents)
    {
        $metadataFileName = self::generateMetadataFileName($fileName);
        if (!file_exists($metadataFileName))
        {
            return false;
        }

        if (crc32($fileContents) != file_get_contents($metadataFileName))
        {
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

    /* PARSING FUNCTIONS */

    private static function parseEcho(&$str)
    {
        $str = str_replace('{{', '<?php echo', $str);
        $str = str_replace('}}', '; ?>', $str);
    }
}
?>
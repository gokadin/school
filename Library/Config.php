<?php namespace Library;

use DOMDocument;

class Config
{
    protected static $vars = array();
    
    public static function get($var)
    {
        if (!self::$vars)
        {
            $xml = new DOMDocument;
            $xml->load(__DIR__.'/../Config/app.xml');
            
            $elements = $xml->getElementsByTagName('define');
            
            foreach ($elements as $element)
                self::$vars[$element->getAttribute('var')] = $element->getAttribute('value');
        }
        
        if (isset(self::$vars[$var]))
            return self::$vars[$var];

        return null;
    }

    public static function temporary($var, $value)
    {
        self::$vars['testing'] = true;
    }
}

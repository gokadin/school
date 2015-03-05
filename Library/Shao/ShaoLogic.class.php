<?php namespace Library\Shao;

class ShaoLogic
{
    public static function getValidLogicNames()
    {
        return array(
            'if',
            'endif',
            'else',
            'elseif',
            'for',
            'endfor',
            'foreach',
            'endforeach',
            'while',
            'endwhile'
        );
    }

    public static function convertToFunctionName($logicName)
    {
        if (!in_array($logicName, self::getValidLogicNames()))
            return false;

        return 'logic'.ucfirst(strtolower($logicName));
    }

    public static function logicIf($string)
    {
        return '<?php if ('.$string.') { ?>';
    }

    public static function logicEndif()
    {
        return self::closingBracket();
    }

    public static function logicElseif($string)
    {
        return '<?php } else if ('.$string.') { ?>';
    }

    public static function logicElse()
    {
        return '<?php } else { ?>';
    }

    public static function logicFor($string)
    {
        return '<?php for ('.$string.') { ?>';
    }

    public static function logicEndfor()
    {
        return self::closingBracket();
    }

    public static function logicForeach($string)
    {
        return '<?php foreach ('.$string.') { ?>';
    }

    public static function logicEndforeach()
    {
        return self::closingBracket();
    }

    public static function logicWhile($string)
    {
        return '<?php while('.$string.') { ?>';
    }

    public static function logicEndwhile()
    {
        return self::closingBracket();
    }

    private static function closingBracket()
    {
        return '<?php } ?>';
    }
}

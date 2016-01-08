<?php

if (!function_exists('currentRoute()'))
{
    function currentRoute()
    {
        return \Library\Facades\Router::current()->name();
    }
}

if (!function_exists('currentNameContains'))
{
    function currentNameContains($str)
    {
        return \Library\Facades\Router::currentNameContains($str);
    }
}

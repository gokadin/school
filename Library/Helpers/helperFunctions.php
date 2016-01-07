<?php

use Library\Http\View;
use Library\Facades\ViewFactory;

if (!function_exists('asset'))
{
    function asset($file)
    {
        return '/assets/'.$file;
    }
}

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

if (!function_exists('redirect404'))
{
    function redirect404()
    {
        header('HTTP/1.0 404 Not Found');

        $view = new View('errors.404');
        echo $view->send();
        exit();
    }
}

if (!function_exists('back'))
{
    function back()
    {
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit();
    }
}
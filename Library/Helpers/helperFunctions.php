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

if (!function_exists('view'))
{
    function view($viewFile, array $data = array())
    {
        return new View($viewFile, $data);
    }
}

if (!function_exists('viewFactoryStartSection'))
{
    function viewFactoryStartSection($name)
    {
        ViewFactory::startSection($name);
    }
}

if (!function_exists('viewFactoryEndSection'))
{
    function viewFactoryEndSection()
    {
        ViewFactory::endSection();
    }
}

if (!function_exists('viewFactoryYield'))
{
    function viewFactoryYield($name)
    {
        echo ViewFactory::getSection($name);
    }
}

if (!function_exists('generateToken'))
{
    function generateToken()
    {
        return \Library\Facades\Session::generateToken();
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
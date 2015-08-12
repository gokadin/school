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

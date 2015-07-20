<?php

use Library\Facades\App;
use Library\Http\View;
use Library\Facades\ViewFactory;

function asset($file)
{
    return App::basePath().'Resources/Assets/'.$file;
}

function view($viewFile, array $data = array())
{
    return new View($viewFile, $data);
}

function viewFactoryStartSection($name)
{
    ViewFactory::startSection($name);
}

function viewFactoryEndSection()
{
    ViewFactory::endSection();
}

function viewFactoryYield($name)
{
    echo ViewFactory::getSection($name);
}

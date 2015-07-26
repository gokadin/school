<?php

namespace Library\Http;

use Library\Config;
use Library\Facades\Response as RedirectResponse;
use Library\Facades\Router;

class Redirect
{
    public function back()
    {
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit();
    }

    public function to($name, $params = null)
    {
        if (!is_null($params) && !is_array($params))
        {
            $params = [$params];
        }

        $uri = Router::getUri($name, $params);

        header('Location: '.$uri);
        exit();
    }

    public function redirect404()
    {
        RedirectResponse::addHeader('HTTP/1.0 404 Not Found');

        echo view('errors.404')->send();
        exit();
    }
}
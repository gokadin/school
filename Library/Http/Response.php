<?php

namespace Library\Http;

use Library\Facades\Page;
use Library\Routing\Router;

class Response
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function setCookie($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $http_only = true)
    {
        set_cookie($name, $value, $expire, $path, $domain, $secure, $http_only);
    }

    public function back()
    {
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit();
    }

    public function route($name, $params = [])
    {
        $uri = $this->router->getUri($name, $params);

        header('Location: '.$uri);
        exit();
    }

    public function redirect($uri)
    {
        header('Location: '.$uri);
        exit();
    }

    public function redirect404()
    {
        header('HTTP/1.0 404 Not Found');

        $view = new View('errors.404');
        echo $view->send();
        exit();
    }
}

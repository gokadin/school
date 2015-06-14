<?php namespace Library;

use Library\Facades\Page;

class Response
{
    public function addHeader($header)
    {
        header($header);
    }

    public function redirect($location)
    {
        header('Location: '.$location);
        exit();
    }

    public function toAction($location, $args = null)
    {
        $this->redirect(\Library\Facades\Router::actionToPath($location, $args));
    }

    public function back()
    {
        header('Location: '.$_SESSION['HTTP_REFERER']);
        exit();
    }

    public function redirect404()
    {
        \Library\Facades\Page::setContentFile(__DIR__.'/../Errors/404.html');

        $this->addHeader('HTTP/1.0 404 Not Found');

        $this->send();
    }

    public function send()
    {
        exit(Page::getGeneratedPage());
    }

    public function setCookie($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $http_only = true)
    {
        set_cookie($name, $value, $expire, $path, $domain, $secure, $http_only);
    }
}

<?php
namespace Library;

use Library\Facades\Session;

class HTTPResponse extends ApplicationComponent
{
    protected $page;
    
    public function addHeader($header)
    {
        header($header);
    }

    public function redirect($location, array $args = null)
    {
        if ($args != null)
        {
            Session::setErrors($args);
        }

        header('Location: '.$location);
        exit();
    }

    public function toAction($location, array $args = null)
    {
        $this->redirect($this->app->router()->getUrlFromAction($location), $args);
    }

    public function back()
    {
        header("location:javascript://history.go(-1)");
        exit();
    }

    public function redirect404()
    {
        $this->page = new Page($this->app);
        $this->page->setContentFile(__DIR__.'/../Errors/404.html');

        $this->addHeader('HTTP/1.0 404 Not Found');

        $this->send();
    }

    public function send()
    {
        exit($this->page->getGeneratedPage());
    }

    public function setPage(Page $page)
    {
        $this->page = $page;
    }

    public function setCookie($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $http_only = true)
    {
        set_cookie($name, $value, $expire, $path, $domain, $secure, $http_only);
    }
}
?>
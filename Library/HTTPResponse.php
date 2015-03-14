<?php namespace Library;

class HTTPResponse extends ApplicationComponent
{
    protected $page;
    
    public function addHeader($header)
    {
        header($header);
    }

    public function redirect($location, $args = null)
    {
        if ($args != null)
            header('Location: '.$location.  $args);
        else
            header('Location: '.$location);

        exit();
    }

    public function toAction($location, $args = null)
    {
        $this->redirect($this->app->router()->getUrlFromAction($location), $args);
    }

    public function back()
    {
        header('Location: '.$_SESSION['HTTP_REFERER']);
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

<?php namespace Library;

class HTTPRequest extends ApplicationComponent
{
    public function cookieData($key)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    }

    public function cookieExists($key)
    {
        return isset($_COOKIE[$key]);
    }

    public function getData($key)
    {
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }

    public function getExists($key)
    {
        return isset($_GET[$key]);
    }

    public function method()
    {
        if ($this->postExists('_method'))
        {
            $method = $this->postData('_method');
            switch (strtoupper($method))
            {
                case 'PUT':
                    return 'PUT';
                case 'PATCH':
                    return 'PATCH';
                case 'DELETE':
                    return 'DELETE';
            }
        }

        return $_SERVER['REQUEST_METHOD'];
    }

    public function postData($key)
    {
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }

    public function postExists($key)
    {
        return isset($_POST[$key]);
    }
    
    public function fileData($key)
    {
        return isset($_FILES[$key]) ? $_FILES[$key] : null;
    }
    
    public function fileExists($key)
    {
        return isset($_FILES[$key]);
    }

    public function requestURI()
    {
            return $_SERVER['REQUEST_URI'];
    }
}

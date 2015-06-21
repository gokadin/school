<?php namespace Library;

class Request
{
    public function __get($key)
    {
        switch ($this->method())
        {
            case 'GET':
                return isset($_GET[$key]) ? $_GET[$key] : null;
            case 'POST':
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
                return isset($_POST[$key]) ? $_POST[$key] : null;
        }

        return null;
    }

    public function all()
    {
        switch ($this->method())
        {
            case 'GET':
                return $_GET;
            case 'POST':
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
                return $_POST;
        }

        return null;
    }

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
                default:
                    return 'POST';
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
        $requestUri = $_SERVER['REQUEST_URI'];

        if (\Library\Config::get('env') != 'debug')
            return $requestUri;

        $pos = strpos($requestUri, '?XDEBUG_SESSION_START=');
        if ($pos !== false)
            $requestUri = substr($requestUri, 0, $pos);

        return $requestUri;
    }
}

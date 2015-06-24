<?php namespace Library;

class Request
{
    public function instance()
    {
        return $this;
    }

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
                return $this->excludeFrameworkVariablesFromAll($_GET);
            case 'POST':
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
                return $this->excludeFrameworkVariablesFromAll($_POST);
        }

        return null;
    }

    private function excludeFrameworkVariablesFromAll($arr)
    {
        $results = array();
        foreach ($arr as $key => $value)
        {
            if ($key != '_method' && $key != '_token')
                $results[$key] = $value;
        }

        return $results;
    }

    public function cookieData($key)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    }

    public function cookieExists($key)
    {
        return isset($_COOKIE[$key]);
    }

    public function data($key)
    {
        switch ($this->method())
        {
            case 'GET':
                return isset($_GET[$key]) ? $_GET[$key] : null;
            default:
                return isset($_POST[$key]) ? $_POST[$key] : null;
        }
    }

    public function dataExists($key)
    {
        switch ($this->method())
        {
            case 'GET':
                return isset($_GET[$key]);
            default:
                return isset($_POST[$key]);
        }
    }

    public function method()
    {
        if (isset($_POST['_method']))
        {
            $method = $_POST['_method'];
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

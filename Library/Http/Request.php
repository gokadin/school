<?php

namespace Library\Http;

class Request
{
    protected $data = null;
    protected $method = null;

    protected function getDataFromSource()
    {
        if ($this->data != null)
            return $this->data;

        if ($this->isJson())
        {
            $this->data = $this->getDecodedJson();
            return $this->data;
        }

        switch ($this->method())
        {
            case 'GET':
                $this->data = $_GET;
                break;
            case 'POST':
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
                $this->data = $_POST;
                break;
        }

        return $this->data;
    }

    public function isJson()
    {
        if (!strpos($this->header('CONTENT_TYPE'), '/json'))
            return false;

        return true;
    }

    protected function getDecodedJson()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function __get($key)
    {
        $data = $this->getDataFromSource();
        return isset($data[$key]) ? $data[$key] : null;
    }

    public function all()
    {
        return $this->excludeFrameworkVariablesFromAll($this->getDataFromSource());
    }

    protected function excludeFrameworkVariablesFromAll($arr)
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
        $data = $this->getDataFromSource();
        return isset($data[$key]) ? $data[$key] : null;
    }

    public function dataExists($key)
    {
        $data = $this->getDataFromSource();
        return isset($data[$key]);
    }

    public function method()
    {
        if ($this->method != null)
        {
            return $this->method;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'GET')
            return $this->method = 'GET';

        if (isset($_POST['_method']))
        {
            return $this->method = strtoupper($_POST['_method']);
        }

        return $this->method = $_SERVER['REQUEST_METHOD'];
    }
    
    public function fileData($key)
    {
        return isset($_FILES[$key]) ? $_FILES[$key] : null;
    }
    
    public function fileExists($key)
    {
        return isset($_FILES[$key]);
    }

    public function uri()
    {
        $requestUri = $_SERVER['REQUEST_URI'];

        if (\Library\Facades\Config::get('env') != 'debug')
            return $requestUri;

        $pos = strpos($requestUri, '?XDEBUG_SESSION_START=');
        if ($pos !== false)
            $requestUri = substr($requestUri, 0, $pos);

        return $requestUri;
    }

    public function header($key)
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }
}

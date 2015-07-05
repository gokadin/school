<?php namespace Library;

class Request
{
    private $data = null;

    public function instance()
    {
        return $this;
    }

    private function getDataFromSource()
    {
        if ($this->data != null)
            return $this->data;

        if ($this->isJson())
            return $this->data = $this->getDecodedJson();

        switch ($this->method())
        {
            case 'GET':
                return $this->data = $_GET;
            case 'POST':
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
                return $this->data = $_POST;
        }

        return null;
    }

    public function isJson()
    {
        if (!strpos($this->header('CONTENT_TYPE'), '/json'))
            return false;

        return true;
    }

    private function getDecodedJson()
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

    public function header($key)
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }
}

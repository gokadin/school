<?php

namespace Library\Testing;

use PHPUnit_Framework_TestCase;
use Library\Facades\Session;
use Library\Facades\Facade;

require __DIR__.'/../../Bootstrap/autoload.php';

class TestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        putenv('APP_ENV=testing');

        Facade::resetResolvedInstances();
    }

    public function action($method, $controllerAndAction, $arguments = [], $includeToken = true)
    {
        list($controllerName, $methodName) = explode('@', $controllerAndAction);
        $controllerName = '\\App\\Http\\Controllers\\'.$controllerName;
        $controller = new $controllerName();

        $_SERVER['REQUEST_METHOD'] = $method;
        switch ($method)
        {
            case 'GET':
                $_GET = array_merge($_GET, $arguments);
                break;
            default:
                $_POST = array_merge($_POST, $arguments);
                $_POST['_method'] = $method;
                if ($includeToken)
                {
                    $_POST['_token'] = Session::generateToken();
                }
                break;
        }

        if ($method == 'GET' && sizeof($arguments) > 0)
        {
            return call_user_func_array([$controller, $methodName], $arguments);
        }

        return $controller->$methodName();
    }
}
<?php

namespace Library\Http;

use Library\Facades\Page;

class Response
{
    public function addHeader($header)
    {
        header($header);
    }

    public function setCookie($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $http_only = true)
    {
        set_cookie($name, $value, $expire, $path, $domain, $secure, $http_only);
    }
}

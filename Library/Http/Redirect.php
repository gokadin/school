<?php

namespace Library\Http;

use Library\Config;

class Redirect
{
    public function back()
    {
        // TODO: REMOVE FROM HERE ***************************
        if (Config::get('testing') == 'true' || Config::get('frameworkTesting') == 'true')
            return;

        header('Location: '.$_SESSION['HTTP_REFERER']);
        exit();
    }
}
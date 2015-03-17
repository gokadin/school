<?php namespace Library;

class HTML
{
    public function path($action, $args = null)
    {
        return \Library\Facades\Router::actionToPath($action, $args);
    }
}
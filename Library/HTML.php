<?php namespace Library;

class HTML
{
    public function path($action, $args)
    {
        return \Library\Facades\HTML::actionToPath($action, $args);
    }
}
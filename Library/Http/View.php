<?php

namespace Library\Http;

class View
{
    const BASE_PATH = '/Resources/Views';

    protected $content;

    public function __construct($view, array $data)
    {
        // handledata *****888
        $this->content = $this->processView($view);
    }

    public function send()
    {
        echo $this->content;
    }

    protected function processView($view)
    {
        $view = self::BASE_PATH.'/'.str_replace('.', '/', $view);

        if (file_exists($view.'.shao.php'))
        {

        }
        else if (file_exists($view.'.shao.html'))
        {

        }
        else if (file_exists($view.'.php'))
        {

        }
        else if (file_exists($view.'.html'))
        {

        }
    }
}
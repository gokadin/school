<?php

use Library\Http\View;

function view($viewFile, array $data = array())
{
    return new View($viewFile, $data);
}
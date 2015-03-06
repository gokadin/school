<?php
function autoload($class) {
    require_once(str_replace('\\', '/', $class).'.php');
}

spl_autoload_register('autoload');

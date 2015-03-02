<?php
function autoload($class) {
    require_once(str_replace('\\', '/', $class).'.class.php');
}

spl_autoload_register('autoload');
?>
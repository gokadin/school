<?php

require __DIR__.'/bootstrap/autoload.php';

$app = new Applications\Backend\BackendApplication('Backend');
$app->run();

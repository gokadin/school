<?php

require __DIR__.'/bootstrap/autoload.php';

$app = new Applications\Frontend\FrontendApplication('Frontend');
$app->run();

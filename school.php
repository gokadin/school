<?php

require __DIR__.'/bootstrap/autoload.php';

$app = new Applications\School\SchoolApplication('School');
$app->run();

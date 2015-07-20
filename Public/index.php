<?php

require __DIR__ . '/../Bootstrap/autoload.php';

$app = new Library\Application();
echo $app->basePath();
$app->processRoute();

$app->sendView();

<?php

require __DIR__.'/bootstrap/autoload.php';

$app = new Library\Application();

$app->processRoute();

$app->sendView();

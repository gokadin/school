<?php

require __DIR__ . '/../Bootstrap/autoload.php';

$app = new Library\Application();

$app->processRoute();

$app->sendView();

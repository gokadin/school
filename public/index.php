<?php
error_reporting(2);
require __DIR__ . '/../Bootstrap/autoload.php';

$app = new Library\Application();

$app->processRoute();

$app->sendView();

#!/usr/bin/env php
<?php

require __DIR__.'/../../../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Library\DataMapper\Console\CreateSchema;

$app = new Application();

$config = require __DIR__.'/../../../Config/datamapper.php';
$app->add(new CreateSchema($config));

$app->run();

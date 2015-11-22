#!/usr/bin/env php
<?php

require __DIR__.'/../../../Bootstrap/autoload.php';

use Symfony\Component\Console\Application;
use Library\DataMapper\Console\Modules\CreateSchema;
use Library\DataMapper\Console\Modules\DropSchema;

$app = new Application();

$config = require __DIR__.'/../../../Config/datamapper.php';
$app->add(new CreateSchema($config));
$app->add(new DropSchema($config));

$app->run();

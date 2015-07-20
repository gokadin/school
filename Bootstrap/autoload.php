<?php

date_default_timezone_set('America/Montreal');

require __DIR__.'/../Library/Helpers/helperFunctions.php';
require __DIR__.'/../Library/Configuration/envFunctions.php';

configureEnvironment();

require __DIR__.'/../vendor/autoload.php';

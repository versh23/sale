#!/usr/bin/env php
<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/src/app.php';

use Symfony\Component\Console\Application;
$console = new Application();

$console->add(new Sale\Command\DatabaseSetupCommand($app['db']));

$console->run();
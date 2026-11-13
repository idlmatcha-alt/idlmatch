<?php

define('LARAVEL_START', microtime(true));

// Local (localhost / Herd) and production (Hostinger) both use this file.
// Prefer the local copy when it exists so the app runs at localhost too.
$autoload = __DIR__ . '/vendor/autoload.php';
$bootstrap = __DIR__ . '/bootstrap/app.php';

if (! file_exists($autoload)) {
    $autoload = '/home/u599271327/idlmatch_app/vendor/autoload.php';
    $bootstrap = '/home/u599271327/idlmatch_app/bootstrap/app.php';
}

require $autoload;

$app = require_once $bootstrap;

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle($request = Illuminate\Http\Request::capture());

$response->send();

$kernel->terminate($request, $response);
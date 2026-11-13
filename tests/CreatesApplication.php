<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        putenv('APP_ENV=testing');
        $_ENV['APP_ENV'] = 'testing';
        $_SERVER['APP_ENV'] = 'testing';

        $app = require __DIR__.'/../bootstrap/app.php';
        $app->detectEnvironment(fn () => 'testing');

        $app->make(Kernel::class)->bootstrap();
        $app->instance('env', 'testing');
        config(['app.env' => 'testing']);

        return $app;
    }
}

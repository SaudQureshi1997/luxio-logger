<?php

namespace Elphis\Providers;

use Elphis\Providers\ServiceProvider;
use Elphis\Utils\Logger;

class LoggerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Logger::class, function () {
            return new Logger(
                storage_path('logs')
            );
        });
    }
}

<?php

namespace Elphis\Support\Facades;

use Elphis\Logger as RootLogger;
use Elphis\Support\Facades\Facade;

class Logger extends Facade
{
    public static function getFacadeAccessor()
    {
        return RootLogger::class;
    }
}

<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;

class CustomFormatter
{
    /**
     * Customize the given logger instance.
     *
     * @param  \Illuminate\Log\Logger  $logger
     * @return void
     */
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new LineFormatter("%message%\n"));
        }
    }
}

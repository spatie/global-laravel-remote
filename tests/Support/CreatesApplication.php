<?php

namespace Tests\Support;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    public function createApplication()
    {
        $app = require __DIR__.'/../../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Return error reporting to PHPUnit after Laravel Zero installs its handlers.
        restore_error_handler();
        restore_exception_handler();

        return $app;
    }
}

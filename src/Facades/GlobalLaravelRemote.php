<?php

namespace Spatie\GlobalLaravelRemote\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\GlobalLaravelRemote\GlobalLaravelRemote
 */
class GlobalLaravelRemote extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Spatie\GlobalLaravelRemote\GlobalLaravelRemote::class;
    }
}

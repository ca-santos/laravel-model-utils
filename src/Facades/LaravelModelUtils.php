<?php

namespace CaueSantos\LaravelModelUtils\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelModelUtils extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-model-utils';
    }
}

<?php

namespace CaueSantos\LaravelModelUtils\Tests;

use CaueSantos\LaravelModelUtils\Facades\LaravelModelUtils;
use CaueSantos\LaravelModelUtils\ServiceProvider;
use Orchestra\Testbench\TestCase;

class LaravelModelUtilsTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'laravel-model-utils' => LaravelModelUtils::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}

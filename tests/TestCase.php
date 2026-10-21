<?php

declare(strict_types=1);

namespace FieldVn\FilamentThemes\Tests;

use FieldVn\FilamentThemes\Tests\Fixtures\AdminPanelProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Register this package, Filament, Livewire and Blade Icons the way an app does: from their "extra.laravel"
     * providers, as listed by `testbench package:discover` (composer post-autoload-dump).
     */
    protected $enablesPackageDiscoveries = true;

    protected function getPackageProviders($app): array
    {
        return [AdminPanelProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('t', 32)));
    }
}

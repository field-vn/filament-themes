<?php

declare(strict_types=1);

namespace FieldVn\FilamentThemes;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

final class FilamentThemesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Registered here (not in the plugin) so `filament:assets` publishes every stylesheet even before a panel boots.
        // loadedOnRequest: keep them out of @filamentStyles; the plugin prints core + its preset only for panels using it.
        $names = ['core', ...array_map(static fn (Preset $preset): string => $preset->stylesheet(), Preset::cases())];

        FilamentAsset::register(array_map(
            static fn (string $name): Css => Css::make(FilamentThemes::stylesheetId($name), FilamentThemes::stylesheetPath($name))->loadedOnRequest(),
            $names,
        ), FilamentThemes::PACKAGE);
    }
}

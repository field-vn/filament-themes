<?php

declare(strict_types=1);

namespace FieldVn\FilamentThemes\Presets\Shadcn;

use FieldVn\FilamentThemes\Support\Palette;
use Filament\Support\Colors\Color;

/**
 * shadcn/ui "neutral" base color (ui.shadcn.com/r/colors/neutral.json, MIT).
 */
final class Colors
{
    /**
     * @return array<string, array<int, string>>
     */
    public static function all(): array
    {
        return [
            // shadcn's primary is near-black (oklch 0.205), its hover a step lighter. Buttons use 600 with a 500
            // hover, so the neutral scale is shifted to put those two exactly there. Dark mode inverts it in CSS.
            'primary' => Palette::fromShades([
                50 => 'oklch(0.985 0 0)',
                100 => 'oklch(0.97 0 0)',
                200 => 'oklch(0.922 0 0)',
                300 => 'oklch(0.708 0 0)',
                400 => 'oklch(0.439 0 0)',
                500 => 'oklch(0.269 0 0)',
                600 => 'oklch(0.205 0 0)',
                700 => 'oklch(0.145 0 0)',
                800 => 'oklch(0.12 0 0)',
                900 => 'oklch(0.1 0 0)',
                950 => 'oklch(0.08 0 0)',
            ]),
            'danger' => Color::Red,
            'warning' => Color::Amber,
            'success' => Color::Green,
            'info' => Color::Blue,
            'gray' => Color::Neutral,
        ];
    }
}

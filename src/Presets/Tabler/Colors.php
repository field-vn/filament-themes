<?php

declare(strict_types=1);

namespace FieldVn\FilamentThemes\Presets\Tabler;

use FieldVn\FilamentThemes\Support\Palette;
use Filament\Support\Colors\Color;

/**
 * Tabler 1.x palette ("slate" theme base), from @tabler/core core/scss/_variables.scss (MIT).
 */
final class Colors
{
    public const string BLUE = '#066fd1';

    public const string RED = '#d63939';

    public const string YELLOW = '#f59f00';

    public const string GREEN = '#2fb344';

    public const string AZURE = '#4299e1';

    /**
     * @return array<string, array<int, string>>
     */
    public static function all(): array
    {
        return [
            // Buttons use shade 600: pin Tabler blue there so they show the exact brand color with white text.
            'primary' => Palette::anchored(self::BLUE, 600),
            'danger' => Palette::anchored(self::RED),
            'warning' => Palette::anchored(self::YELLOW),
            'success' => Palette::anchored(self::GREEN),
            'info' => Palette::anchored(self::AZURE),
            'gray' => Color::Slate,
        ];
    }
}

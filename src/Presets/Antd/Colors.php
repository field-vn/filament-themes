<?php

declare(strict_types=1);

namespace FieldVn\FilamentThemes\Presets\Antd;

use FieldVn\FilamentThemes\Support\Palette;

/**
 * Ant Design 5 palettes, from @ant-design/colors 7 (MIT): ten steps per color, "-6" being the base color.
 */
final class Colors
{
    public const array BLUE = ['#e6f4ff', '#bae0ff', '#91caff', '#69b1ff', '#4096ff', '#1677ff', '#0958d9', '#003eb3', '#002c8c', '#001d66'];

    public const array RED = ['#fff1f0', '#ffccc7', '#ffa39e', '#ff7875', '#ff4d4f', '#f5222d', '#cf1322', '#a8071a', '#820014', '#5c0011'];

    public const array GOLD = ['#fffbe6', '#fff1b8', '#ffe58f', '#ffd666', '#ffc53d', '#faad14', '#d48806', '#ad6800', '#874d00', '#613400'];

    public const array GREEN = ['#f6ffed', '#d9f7be', '#b7eb8f', '#95de64', '#73d13d', '#52c41a', '#389e0d', '#237804', '#135200', '#092b00'];

    /** antd's neutral scale (colorBgLayout #f5f5f5, colorBorder #d9d9d9, colorBorderSecondary #f0f0f0 …). */
    public const array GRAY = ['#fafafa', '#f5f5f5', '#f0f0f0', '#d9d9d9', '#bfbfbf', '#8c8c8c', '#595959', '#434343', '#262626', '#1f1f1f', '#141414'];

    /**
     * @return array<string, array<int, string>>
     */
    public static function all(): array
    {
        return [
            'primary' => self::scale(self::BLUE, 5),
            // colorError is red-5 (#ff4d4f), not the red-6 base.
            'danger' => self::scale(self::RED, 4),
            'warning' => self::scale(self::GOLD, 5),
            'success' => self::scale(self::GREEN, 5),
            'info' => self::scale(self::BLUE, 5),
            'gray' => Palette::fromShades(array_combine([50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950], self::GRAY)),
        ];
    }

    /**
     * Ten antd steps onto Filament's eleven shades: the token color ($steps[$base]) on 600, where buttons sit, and
     * the step before it, antd's hover color, on 500 (Filament's button hover). Shades lighter than the first step
     * are blended toward white.
     *
     * @param  list<string>  $steps
     * @return array<int, string>
     */
    private static function scale(array $steps, int $base): array
    {
        $shades = [];

        foreach ([50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950] as $position => $shade) {
            $index = $base + $position - 6;
            $shades[$shade] = $index >= 0 ? $steps[min(9, $index)] : self::tint($steps[0], -$index);
        }

        return Palette::fromShades($shades);
    }

    /** Halve the distance to white, $times times. */
    private static function tint(string $hex, int $times): string
    {
        $channels = array_map(hexdec(...), str_split(substr($hex, 1), 2));

        for ($i = 0; $i < $times; $i++) {
            $channels = array_map(static fn (int|float $channel): int => intdiv((int) $channel + 255, 2), $channels);
        }

        return vsprintf('#%02x%02x%02x', $channels);
    }
}

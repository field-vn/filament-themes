<?php

declare(strict_types=1);

namespace FieldVn\FilamentThemes\Support;

use Filament\Support\Colors\Color;
use InvalidArgumentException;

final class Palette
{
    /**
     * Filament's palette curve (Color::generatePalette()): [lightness, chroma] per shade.
     */
    private const array CURVE = [
        50 => [0.97717647058824, 0.01395454545455],
        100 => [0.95035294117647, 0.03272727272727],
        200 => [0.90547058823529, 0.06318181818182],
        300 => [0.84047058823529, 0.10604545454546],
        400 => [0.75352941176471, 0.15027272727273],
        500 => [0.68270588235294, 0.17009090909091],
        600 => [0.59782352941176, 0.16913636363636],
        700 => [0.51494117647059, 0.14940909090909],
        800 => [0.44611764705882, 0.12331818181818],
        900 => [0.39458823529412, 0.09963636363636],
        950 => [0.27788235294118, 0.07136363636364],
    ];

    /**
     * Like Color::hex(), but keeps the color's own lightness and chroma: Filament only keeps the hue, so a
     * brand color never appears as-is. The color lands on $shade (default: the shade closest in lightness),
     * and its neighbours shift along with it, less the further away they are.
     *
     * @return array<int, string>
     */
    public static function anchored(string $color, ?int $shade = null): array
    {
        [$lightness, $chroma, $hue] = sscanf(Color::convertToOklch($color), 'oklch(%f %f %f)');

        $shade ??= self::closestShade((float) $lightness);

        if (! array_key_exists($shade, self::CURVE)) {
            throw new InvalidArgumentException("Invalid shade [{$shade}]: use one of 50, 100, 200 … 900, 950.");
        }

        [$anchorLightness, $anchorChroma] = self::CURVE[$shade];

        $isAchromatic = $chroma < 0.03;
        $lightnessShift = $lightness - $anchorLightness;
        $chromaScale = $chroma / $anchorChroma;
        $anchorIndex = array_search($shade, array_keys(self::CURVE), true);

        $palette = [];

        foreach (array_keys(self::CURVE) as $index => $key) {
            [$curveLightness, $curveChroma] = self::CURVE[$key];
            $weight = max(0.0, 1 - 0.15 * abs($index - $anchorIndex));

            $l = min(0.99, max(0.1, $curveLightness + $lightnessShift * $weight));
            $c = $isAchromatic ? 0 : min(0.37, max(0.0, $curveChroma * (1 + ($chromaScale - 1) * $weight)));

            $palette[$key] = [$l, $c];
        }

        // Buttons pair a 600 background with a 500 hover and need the same text color on both: keep 500 close.
        if ($shade === 600) {
            $palette[500][0] = min($palette[500][0], $palette[600][0] + 0.06);
        }

        return array_map(
            static fn (array $lc): string => sprintf('oklch(%.4f %.4f %.3f)', $lc[0], $lc[1], $hue),
            $palette,
        );
    }

    /**
     * A palette from explicit colors, one per shade (hex, rgb() or oklch()), e.g. a design system's own scale.
     *
     * @param  array<int, string>  $shades  50, 100, 200 … 900, 950
     * @return array<int, string>
     */
    public static function fromShades(array $shades): array
    {
        if (array_keys($shades) !== array_keys(self::CURVE)) {
            throw new InvalidArgumentException('A palette needs exactly the shades 50, 100, 200 … 900, 950, in order.');
        }

        return array_map(Color::convertToOklch(...), $shades);
    }

    private static function closestShade(float $lightness): int
    {
        $closest = 500;

        foreach (self::CURVE as $key => [$curveLightness]) {
            if (abs($curveLightness - $lightness) < abs(self::CURVE[$closest][0] - $lightness)) {
                $closest = $key;
            }
        }

        return $closest;
    }
}

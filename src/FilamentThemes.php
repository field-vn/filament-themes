<?php

declare(strict_types=1);

namespace FieldVn\FilamentThemes;

use Composer\InstalledVersions;
use FieldVn\FilamentThemes\Pages\ThemePreview;
use FieldVn\FilamentThemes\Widgets\PreviewStats;
use Filament\Actions\Action;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Enums\Alignment;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentIcon;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use InvalidArgumentException;
use LogicException;

final class FilamentThemes implements Plugin
{
    public const string PACKAGE = 'field-vn/filament-themes';

    private Preset $preset = Preset::Tabler;

    private bool $isHorizontal = false;

    private bool $hasIcons = false;

    private bool $hasPreviewPage = false;

    private bool $alignsModals = true;

    /** Null: the preset's default. */
    private bool|string|null $darkSidebar = null;

    /** @var array<string, string> */
    private static array $assetVersions = [];

    public static function make(): static
    {
        return app(self::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(self::class)->getId());

        return $plugin;
    }

    public static function stylesheetPath(string $name): string
    {
        return dirname(__DIR__)."/resources/dist/{$name}.css";
    }

    /**
     * Published as css/field-vn/filament-themes/<name>-<content hash>.css. A new file name per change, instead of
     * Filament's "?v=" query: CDNs set to ignore query strings (Cloudflare's "Ignore query string" caching level)
     * would keep serving the old stylesheet, and a path repository's version never changes.
     */
    public static function stylesheetId(string $name): string
    {
        return $name.'-'.(self::$assetVersions[$name] ??= substr((string) hash_file('xxh128', self::stylesheetPath($name)), 0, 12));
    }

    public function getId(): string
    {
        return 'filament-themes';
    }

    /** Design system: Tabler (default), … */
    public function preset(Preset|string $preset): static
    {
        $this->preset = is_string($preset) ? Preset::from($preset) : $preset;

        return $this;
    }

    /**
     * The preset's horizontal layout: a two-row header (Filament's top navigation) instead of the sidebar.
     */
    public function horizontal(bool $condition = true): static
    {
        $this->isHorizontal = $condition;

        return $this;
    }

    /** Swap Filament's default Heroicons for the preset's icon family. */
    public function icons(bool $condition = true): static
    {
        $this->hasIcons = $condition;

        return $this;
    }

    /**
     * Lay modals out like the design system (on by default): footer buttons on the right, titles and confirmations
     * aligned per Preset::modalAlignment(). Off: Filament's own alignment. An action that sets its own keeps it.
     */
    public function alignModals(bool $condition = true): static
    {
        $this->alignsModals = $condition;

        return $this;
    }

    /** A "Theme preview" page with every common component on sample data, to review the preset. Keep it out of production. */
    public function previewPage(bool $condition = true): static
    {
        $this->hasPreviewPage = $condition;

        return $this;
    }

    /** Dark sidebar (vertical layout only), optionally in a given color, e.g. a brand color: '#1D334F'. */
    public function darkSidebar(bool|string $color = true): static
    {
        if (is_string($color) && preg_match('/^(#[0-9a-f]{3,8}|(rgb|rgba|hsl|hsla|oklch|oklab)\([0-9a-z.,%\/ +-]+\))$/i', $color) !== 1) {
            throw new InvalidArgumentException("Invalid sidebar color [{$color}]: use a hex, rgb(), hsl(), oklch() or oklab() value.");
        }

        $this->darkSidebar = $color;

        return $this;
    }

    public function getPreset(): Preset
    {
        return $this->preset;
    }

    public function isHorizontal(): bool
    {
        return $this->isHorizontal;
    }

    public function hasIcons(): bool
    {
        return $this->hasIcons;
    }

    public function hasDarkSidebar(): bool
    {
        return ! $this->isHorizontal && ($this->darkSidebar ?? $this->preset->hasDarkSidebar()) !== false;
    }

    public function register(Panel $panel): void
    {
        // The preset's layout. Call ->topbar() / ->topNavigation() / ->sidebarWidth() after ->plugin() to override.
        if ($this->isHorizontal) {
            $panel->topNavigation();
        } else {
            $panel->topbar($this->preset->hasTopbar());
        }

        if (($width = $this->preset->sidebarWidth()) !== null) {
            $panel->sidebarWidth($width);
        }

        if ($this->hasPreviewPage) {
            $panel->pages([ThemePreview::class])->livewireComponents([PreviewStats::class]);
        }

        // STYLES_AFTER comes after the panel theme in <head>, so these rules win over Filament's at equal specificity.
        $panel->renderHook(PanelsRenderHook::STYLES_AFTER, fn (): HtmlString => $this->renderStyles());
    }

    public function boot(Panel $panel): void
    {
        // Only fill what the panel left unset, so the project's ->colors() / ->icons() / ->font() win whatever the call order.
        FilamentColor::register(array_diff_key($this->preset->colors(), $panel->getColors()));

        if ($this->hasIcons) {
            // The three icon families weigh about 40 MB together, so a project installs only the one it uses.
            if (! InstalledVersions::isInstalled($package = $this->preset->iconPackage())) {
                throw new LogicException("FilamentThemes::icons() needs the {$this->preset->name} icon set: composer require {$package}");
            }

            FilamentIcon::register(array_diff_key($this->preset->icons(), $panel->getIcons()));
        }

        if ((($font = $this->preset->font()) !== null) && (! $panel->hasCustomFontFamily())) {
            $panel->font($font);
        }

        if ($this->alignsModals) {
            // Dialog buttons go on the right in all three systems; under a centered modal (Tabler's confirmations and
            // modal-sm) they share the width instead. Runs after the project's own Action::configureUsing(): a project
            // default that should win registers with `isImportant: true`.
            $alignment = $this->preset->modalAlignment();

            Action::configureUsing(static fn (Action $action): Action => $action
                ->modalAlignment($alignment)
                ->modalFooterActionsAlignment(static fn (): Alignment => in_array($action->getModalAlignment(), [Alignment::Center, 'center'], true)
                    ? Alignment::Center
                    : Alignment::End));
        }
    }

    private function renderStyles(): HtmlString
    {
        $html = '';

        foreach (['core', $this->preset->stylesheet()] as $name) {
            $href = Str::before(FilamentAsset::getStyleHref(self::stylesheetId($name), self::PACKAGE), '?');
            $html .= '<link href="'.e($href).'" rel="stylesheet" data-navigate-track />';
        }

        if ($this->hasDarkSidebar()) {
            // In <head>, before the body renders: the preset's dark sidebar tokens key off this attribute.
            $html .= '<script>document.documentElement.dataset.ftSidebar = "dark"</script>';

            if (is_string($this->darkSidebar)) {
                $html .= "<style>:root{--ft-sidebar-bg:{$this->darkSidebar}}</style>";
            }
        }

        return new HtmlString($html);
    }
}

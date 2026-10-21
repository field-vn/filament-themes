<?php

declare(strict_types=1);

use Composer\InstalledVersions;
use FieldVn\FilamentThemes\FilamentThemes;
use FieldVn\FilamentThemes\Pages\ThemePreview;
use FieldVn\FilamentThemes\Preset;
use FieldVn\FilamentThemes\Presets\Tabler;
use FieldVn\FilamentThemes\Support\Palette;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Filament\Support\View\Components\ButtonComponent;
use Filament\View\PanelsRenderHook;
use Livewire\Livewire;

/**
 * Boot a throwaway panel the way Filament does on a request (colors, icons, then plugins).
 */
function bootThemedPanel(Closure $configure): Panel
{
    $panel = $configure(Panel::make()->id('themes-test'));
    $panel->boot();

    return $panel;
}

function themeStyles(): string
{
    return FilamentView::renderHook(PanelsRenderHook::STYLES_AFTER)->toHtml();
}

/**
 * Every icon alias the installed Filament declares.
 *
 * @return list<string>
 */
function filamentIconAliases(): array
{
    return collect(glob(dirname((string) InstalledVersions::getInstallPath('filament/filament')).'/*/src/View/*IconAlias.php') ?: [])
        ->flatMap(function (string $file): array {
            preg_match('/^namespace (.+);$/m', (string) file_get_contents($file), $namespace);

            return array_values((new ReflectionClass($namespace[1].'\\'.basename($file, '.php')))->getConstants());
        })
        ->values()
        ->all();
}

it('loads core and the preset stylesheet after the panel theme', function (): void {
    $html = (string) $this->get('/admin/login')->assertOk()->getContent();
    $stylesheet = Filament::getPanel('admin')->getPlugin('filament-themes')->getPreset()->stylesheet();

    $theme = strpos($html, 'css/filament/filament/app.css');
    $core = strpos($html, 'css/field-vn/filament-themes/'.FilamentThemes::stylesheetId('core').'.css');
    $preset = strpos($html, 'css/field-vn/filament-themes/'.FilamentThemes::stylesheetId($stylesheet).'.css');

    expect($theme)->toBeInt()
        ->and($core)->toBeInt()->toBeGreaterThan($theme)
        ->and($preset)->toBeInt()->toBeGreaterThan($core);
});

it('prints core, then the preset stylesheet', function (Preset $preset): void {
    bootThemedPanel(fn (Panel $panel): Panel => $panel->plugin(FilamentThemes::make()->preset($preset)));

    $html = themeStyles();

    expect(strpos($html, FilamentThemes::stylesheetId('core').'.css'))
        ->toBeInt()
        ->toBeLessThan(strpos($html, FilamentThemes::stylesheetId($preset->stylesheet()).'.css'));
})->with(Preset::cases());

it('names each published stylesheet after its content', function (string $name): void {
    $hash = substr((string) hash_file('xxh128', FilamentThemes::stylesheetPath($name)), 0, 12);

    expect(FilamentThemes::stylesheetId($name))->toBe("{$name}-{$hash}");
})->with(fn (): array => ['core', ...array_map(fn (Preset $preset): string => $preset->stylesheet(), Preset::cases())]);

it('defines every token core.css reads', function (Preset $preset): void {
    $core = (string) file_get_contents(FilamentThemes::stylesheetPath('core'));
    $tokens = (string) file_get_contents(FilamentThemes::stylesheetPath($preset->stylesheet()));

    preg_match_all('/var\((--ft-[a-z0-9-]+)\)/', $core, $used);
    preg_match_all('/(--ft-[a-z0-9-]+)\s*:/', $core.$tokens, $defined);

    expect(array_values(array_diff(array_unique($used[1]), $defined[1])))->toBe([]);
})->with(Preset::cases());

it('switches to the dark sidebar only when asked, in the vertical layout', function (FilamentThemes $plugin, bool $dark, bool $color): void {
    bootThemedPanel(fn (Panel $panel): Panel => $panel->plugin($plugin));

    $html = themeStyles();

    expect(str_contains($html, 'dataset.ftSidebar = "dark"'))->toBe($dark)
        ->and(str_contains($html, ':root{--ft-sidebar-bg:#1D334F}'))->toBe($color);
})->with([
    'Tabler: light by default' => fn (): array => [FilamentThemes::make(), false, false],
    'dark' => fn (): array => [FilamentThemes::make()->darkSidebar(), true, false],
    'dark, brand color' => fn (): array => [FilamentThemes::make()->darkSidebar('#1D334F'), true, true],
    'horizontal ignores it' => fn (): array => [FilamentThemes::make()->horizontal()->darkSidebar('#1D334F'), false, false],
]);

it('follows the preset\'s sidebar color by default', function (Preset $preset): void {
    expect(FilamentThemes::make()->preset($preset)->hasDarkSidebar())->toBe($preset->hasDarkSidebar())
        ->and(FilamentThemes::make()->preset($preset)->darkSidebar(false)->hasDarkSidebar())->toBeFalse();
})->with(Preset::cases());

it('applies the preset palette when the panel sets no colors', function (Preset $preset): void {
    bootThemedPanel(fn (Panel $panel): Panel => $panel->plugin(FilamentThemes::make()->preset($preset)));

    expect(FilamentColor::getColors()['primary'])->toBe($preset->colors()['primary'])
        ->and(FilamentColor::getColors()['gray'])->toBe($preset->colors()['gray']);
})->with(Preset::cases());

it('keeps the panel colors whether they are set before or after the plugin', function (Closure $configure): void {
    bootThemedPanel($configure);

    expect(FilamentColor::getColors()['primary'])->toBe(Color::Amber)
        ->and(FilamentColor::getColors()['danger'])->toBe(Tabler\Colors::all()['danger']);
})->with([
    'before' => [fn (Panel $panel): Panel => $panel->colors(['primary' => Color::Amber])->plugin(FilamentThemes::make())],
    'after' => [fn (Panel $panel): Panel => $panel->plugin(FilamentThemes::make())->colors(['primary' => Color::Amber])],
]);

it('renders Tabler primary buttons in Tabler blue with light text', function (): void {
    $primary = Tabler\Colors::all()['primary'];
    $map = ButtonComponent::make()->getColorMap($primary);

    expect($map['bg'])->toBe(600)
        ->and(Color::convertToHex($primary[600]))->toBe('#076fd1');
});

it('anchors a custom color on the requested shade', function (): void {
    $palette = Palette::anchored('#1d334f', 800);

    expect(Color::convertToHex($palette[800]))->toBe('#1d334f')
        ->and(array_keys($palette))->toBe([50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950]);
});

it('builds a palette from explicit shades, and only from a full scale', function (): void {
    $shades = array_combine([50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950], array_fill(0, 11, '#1677ff'));

    expect(Palette::fromShades($shades))->toHaveCount(11)
        ->and(fn () => Palette::fromShades([600 => '#1677ff']))->toThrow(InvalidArgumentException::class);
});

it('maps every icon alias Filament declares', function (Preset $preset): void {
    expect(filamentIconAliases())->not->toBeEmpty()
        ->and(array_values(array_diff(filamentIconAliases(), array_keys($preset->icons()))))->toBe([]);
})->with(Preset::cases());

it('only maps icons that exist', function (string $icon): void {
    expect(svg($icon)->toHtml())->toContain('<svg');
})->with(fn (): array => array_values(array_unique(array_merge(...array_map(
    fn (Preset $preset): array => array_values($preset->icons()),
    Preset::cases(),
)))));

it('swaps icons only when asked, and leaves the panel icons alone', function (): void {
    bootThemedPanel(fn (Panel $panel): Panel => $panel
        ->icons(['panels::global-search.field' => 'tabler-zoom'])
        ->plugin(FilamentThemes::make()->icons()));

    expect(FilamentIcon::resolve('panels::global-search.field'))->toBe('tabler-zoom')
        ->and(FilamentIcon::resolve('tables::search-field'))->toBe('tabler-search');
});

it('keeps Heroicons by default', function (): void {
    bootThemedPanel(fn (Panel $panel): Panel => $panel->plugin(FilamentThemes::make()));

    expect(FilamentIcon::resolve('tables::search-field'))->toBeNull();
});

it('uses the preset\'s vertical layout by default', function (Preset $preset): void {
    $panel = Panel::make()->id('themes-test')->plugin(FilamentThemes::make()->preset($preset));

    expect($panel->hasTopbar())->toBe($preset->hasTopbar())
        ->and($panel->hasTopNavigation())->toBeFalse();
})->with(Preset::cases());

it('switches the panel to top navigation when horizontal', function (Preset $preset): void {
    $panel = Panel::make()->id('themes-test')->plugin(FilamentThemes::make()->preset($preset)->horizontal());

    expect($panel->hasTopNavigation())->toBeTrue()
        ->and($panel->hasTopbar())->toBeTrue();
})->with(Preset::cases());

it('lets the panel keep its topbar when set after the plugin', function (): void {
    $panel = Panel::make()->id('themes-test')->plugin(FilamentThemes::make())->topbar();

    expect($panel->hasTopbar())->toBeTrue();
});

it('sets the preset font only when the panel has none', function (Preset $preset): void {
    $default = bootThemedPanel(fn (Panel $panel): Panel => $panel->plugin(FilamentThemes::make()->preset($preset)));
    $custom = bootThemedPanel(fn (Panel $panel): Panel => $panel->font('Roboto')->plugin(FilamentThemes::make()->preset($preset)));

    expect($default->getFontFamily())->toBe($preset->font() ?? 'Inter Variable')
        ->and($custom->getFontFamily())->toBe('Roboto');
})->with(Preset::cases());

it('accepts the preset by name', function (): void {
    expect(FilamentThemes::make()->preset('tabler')->getPreset())->toBe(Preset::Tabler);
});

it('rejects a sidebar color that is not a plain color value', function (string $color): void {
    FilamentThemes::make()->darkSidebar($color);
})->throws(InvalidArgumentException::class)->with([
    'css injection' => 'red;}body{display:none',
    'markup' => '</style><script>',
    'named color' => 'navy',
]);

it('registers the preview page only when asked', function (): void {
    $without = Panel::make()->id('themes-test')->plugin(FilamentThemes::make());
    $with = Panel::make()->id('themes-test')->plugin(FilamentThemes::make()->previewPage());

    expect($without->getPages())->not->toContain(ThemePreview::class)
        ->and($with->getPages())->toContain(ThemePreview::class);
});

it('renders the preview page with sample data', function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Livewire::test(ThemePreview::class)
        ->assertOk()
        ->assertSee('Customers')
        ->assertSee('Olivia Martin')
        ->searchTable('Isabella')
        ->assertSee('Isabella Nguyen')
        ->assertDontSee('Olivia Martin');
});

it('right-aligns modal footer actions, unless an action says otherwise', function (): void {
    bootThemedPanel(fn (Panel $panel): Panel => $panel->plugin(FilamentThemes::make()));

    expect(Action::make('a')->getModalFooterActionsAlignment())->toBe(Alignment::End)
        ->and(Action::make('b')->modalFooterActionsAlignment(Alignment::Start)->getModalFooterActionsAlignment())->toBe(Alignment::Start);
});

it('lays confirmation and small modals out the way each preset does', function (Preset $preset, Alignment $modal, Alignment $footer): void {
    bootThemedPanel(fn (Panel $panel): Panel => $panel->plugin(FilamentThemes::make()->preset($preset)));

    foreach ([Action::make('delete')->requiresConfirmation(), Action::make('rename')->modalWidth(Width::Small)] as $action) {
        expect($action->getModalAlignment())->toBe($modal)
            ->and($action->getModalFooterActionsAlignment())->toBe($footer);
    }

    expect(Action::make('edit')->getModalAlignment())->toBe(Alignment::Start)
        ->and(Action::make('edit')->getModalFooterActionsAlignment())->toBe(Alignment::End);
})->with([
    'tabler: centered, full-width buttons' => [Preset::Tabler, Alignment::Center, Alignment::Center],
    'shadcn: AlertDialog' => [Preset::Shadcn, Alignment::Start, Alignment::End],
    'antd: Modal.confirm' => [Preset::Antd, Alignment::Start, Alignment::End],
]);

it('lets an action keep its own modal alignment, footer following it', function (): void {
    bootThemedPanel(fn (Panel $panel): Panel => $panel->plugin(FilamentThemes::make()->preset(Preset::Antd)));

    $action = Action::make('own')->requiresConfirmation()->modalAlignment(Alignment::Center);

    expect($action->getModalAlignment())->toBe(Alignment::Center)
        ->and($action->getModalFooterActionsAlignment())->toBe(Alignment::Center);
});

it('keeps Filament\'s modal alignment when asked to', function (): void {
    bootThemedPanel(fn (Panel $panel): Panel => $panel->plugin(FilamentThemes::make()->preset(Preset::Antd)->alignModals(false)));

    expect(Action::make('edit')->getModalFooterActionsAlignment())->toBeNull()
        ->and(Action::make('delete')->requiresConfirmation()->getModalAlignment())->toBe(Alignment::Center);
});

it('lets a project default registered as important win over the plugin', function (): void {
    Action::configureUsing(fn (Action $action): Action => $action->modalFooterActionsAlignment(Alignment::Start), isImportant: true);

    bootThemedPanel(fn (Panel $panel): Panel => $panel->plugin(FilamentThemes::make()));

    expect(Action::make('edit')->getModalFooterActionsAlignment())->toBe(Alignment::Start);
});

it('names an installed icon package for every preset', function (Preset $preset): void {
    expect(InstalledVersions::isInstalled($preset->iconPackage()))->toBeTrue();
})->with(Preset::cases());

it('opens every modal of the preview page', function (string $action): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Livewire::test(ThemePreview::class)
        ->mountAction($action)
        ->assertActionMounted($action);
})->with(['confirm', 'modal', 'wizard', 'slideOver']);

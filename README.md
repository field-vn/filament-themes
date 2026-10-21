# Filament Themes

**English** | [Tiếng Việt](README.vi.md) | [日本語](README.ja.md)

Make your [Filament 5](https://filamentphp.com) panel look like **Tabler**, **shadcn/ui** or **Ant Design** with a single
line of code. No build step, no Vite config, no custom theme.

Built and maintained by [field.vn](https://field.vn/).

```php
->plugin(FilamentThemes::make()->preset(Preset::Shadcn))
```

## Contents

- [Features](#features)
- [Presets](#presets)
- [Screenshots](#screenshots)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Colors](#colors)
- [Icons](#icons)
- [Logo](#logo)
- [Customizing with CSS variables](#customizing-with-css-variables)
- [Component reference](#component-reference)
- [How it works](#how-it-works)
- [Upgrading Filament](#upgrading-filament)
- [Upgrading from filament-tabler-theme](#upgrading-from-filament-tabler-theme)
- [Limitations](#limitations)
- [Development](#development)
- [License](#license)
- [Credits](#credits)

## Features

- **Three design systems:** Tabler, shadcn/ui and Ant Design, each with its own colors, icons, font and layout.
- **Drop-in:** install the package and register the plugin. Nothing to build or compile.
- **Lightweight:** does not bundle Bootstrap, Radix or antd. The CSS only overrides Filament's `fi-*` classes, so it never
  touches JavaScript (Alpine/Livewire) and works alongside your own `->viteTheme()`.
- **Complete:** light and dark mode, mobile, vertical (sidebar) and horizontal (top navigation) layouts.
- **Respects your settings:** colors, fonts and layout options you set on the panel always win.
- **Preview page:** an optional page that shows every common component, to review a preset at a glance.

## Presets

| Preset | Look | Default layout | Icons | Font |
|---|---|---|---|---|
| `Preset::Tabler` (default) | [Tabler](https://tabler.io): flat cards with thin borders, small uppercase labels, tinted alerts, blue `#066fd1`, slate background | Full-height white sidebar, no topbar | Tabler Icons | Inter |
| `Preset::Shadcn` | [shadcn/ui](https://ui.shadcn.com): neutral tones, black primary button (inverted in dark mode), pill tabs, outlined badges | Light gray sidebar, content in an "inset" card | Lucide | Geist |
| `Preset::Antd` | [Ant Design Pro](https://pro.ant.design): blue `#1677ff`, compact 32px controls, tag-style badges | Dark `#001529` sidebar + white header | Ant Design Icons | System font |

Tables, buttons, modals, notifications and other components are styled to match each system. See the
[component reference](#component-reference) for the details.

## Screenshots

Ant Design preset, on the Theme preview page.

| Light | Dark |
|---|---|
| ![Ant Design preset, Light](images/preview-light.png) | ![Ant Design preset, Dark](images/preview-dark.png) |

<details>
<summary>More: login, modals, notifications, mobile, full page</summary>

| | Light | Dark |
|---|---|---|
| Login | ![Ant Design preset, Login, Light](images/login-light.png) | ![Ant Design preset, Login, Dark](images/login-dark.png) |
| Form modal | ![Ant Design preset, Form modal, Light](images/modal-form-light.png) | ![Ant Design preset, Form modal, Dark](images/modal-form-dark.png) |
| Confirmation dialog | ![Ant Design preset, Confirmation dialog, Light](images/modal-confirm-light.png) | ![Ant Design preset, Confirmation dialog, Dark](images/modal-confirm-dark.png) |
| Wizard | ![Ant Design preset, Wizard, Light](images/modal-wizard-light.png) | ![Ant Design preset, Wizard, Dark](images/modal-wizard-dark.png) |
| Slide-over | ![Ant Design preset, Slide-over, Light](images/slide-over-light.png) | ![Ant Design preset, Slide-over, Dark](images/slide-over-dark.png) |
| Notifications | ![Ant Design preset, Notifications, Light](images/notifications-light.png) | ![Ant Design preset, Notifications, Dark](images/notifications-dark.png) |
| Mobile | ![Ant Design preset, Mobile, Light](images/mobile-light.png) | ![Ant Design preset, Mobile, Dark](images/mobile-dark.png) |
| Full page | ![Ant Design preset, Full page, Light](images/preview-full-light.png) | ![Ant Design preset, Full page, Dark](images/preview-full-dark.png) |

</details>

## Requirements

- PHP 8.3+
- Laravel 12 or 13
- Filament 5

## Installation

**1. Install the package**

```bash
composer require field-vn/filament-themes:^0.1
```

> [!NOTE]
> No version has been tagged yet. Until `v0.1.0` is released, require the development branch instead:
> `composer require field-vn/filament-themes:dev-development`.

**2. (Optional) Install the icon set for your preset**

Only needed if you enable `->icons()`. The three sets weigh about 40 MB together, so the package doesn't install them for
you. Install only the one for your preset:

```bash
composer require secondnetwork/blade-tabler-icons   # Preset::Tabler
composer require mallardduck/blade-lucide-icons     # Preset::Shadcn
composer require codeat3/blade-ant-design-icons     # Preset::Antd
```

**3. Publish the CSS**

```bash
php artisan filament:assets
```

This copies the stylesheets to `public/css/field-vn/filament-themes/`. A standard Filament project already runs
`filament:upgrade` in its `post-autoload-dump` script, which does this automatically. If yours doesn't, run the command
manually after every package update, and add it to your Docker image build.

<details>
<summary>Installing from a private repository</summary>

If the package isn't on Packagist (or the repository is private), add it to your project's `composer.json` before running
`composer require`:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/field-vn/filament-themes" }
]
```

For a private repository, build machines (Docker, CI) need a GitHub token, for example through the `COMPOSER_AUTH`
environment variable.

</details>

## Usage

Register the plugin in your panel provider:

```php
use FieldVn\FilamentThemes\FilamentThemes;
use FieldVn\FilamentThemes\Preset;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->brandLogo(asset('images/logo.svg'))
        ->darkModeBrandLogo(asset('images/logo-white.svg')) // used in dark mode and on dark sidebars
        ->plugin(
            FilamentThemes::make()
                ->preset(Preset::Shadcn)
                ->icons()
                ->previewPage(! app()->isProduction()),
        );
}
```

### Options

| Method | Default | Description |
|---|---|---|
| `->preset(Preset::Tabler)` | `Tabler` | The design system: `Preset::Tabler`, `Preset::Shadcn` or `Preset::Antd`. Strings (`'shadcn'`) work too. |
| `->icons()` | off | Replace Filament's Heroicons with the preset's icon set. Requires the icon package (see [Icons](#icons)). |
| `->horizontal()` | off | Use a two-row white header (top navigation) instead of the sidebar. |
| `->darkSidebar()` | on for Antd | Dark sidebar (vertical layout only). Pass a color for a branded sidebar: `->darkSidebar('#1D334F')`. Pass `false` to turn it off. |
| `->alignModals(false)` | on | Keep Filament's own modal alignment instead of the design system's. |
| `->previewPage()` | off | Add a "Theme preview" page. Keep it out of production. |

### Layout

Each preset sets its own layout when the plugin registers:

| | Tabler | shadcn | Ant Design |
|---|---|---|---|
| `topbar()` | off | off | on |
| `sidebarWidth()` | Filament default | 16rem | 16rem |

To change these, call `->topbar()` or `->sidebarWidth()` on the panel **after** `->plugin()`.

### Preview page

`->previewPage()` adds a page at `/{panel}/theme-preview` that shows nearly every common component on sample data:

- buttons in every color and size, links, icon buttons, tooltips
- stats, callouts, empty state, tabs, infolist
- a form with every field type, including file upload
- a table with search, sorting, filters, bulk actions and pagination (search for something that doesn't exist to see the
  empty state)
- form modal, confirmation dialog, wizard, slide-over, dropdown, notifications

Use it to compare presets or to check the theme after upgrading Filament.

> [!NOTE]
> If you cache components (`php artisan filament:optimize` or `filament:cache-components`), the page has no route until you
> run that command again.

### Modals

By default, modals follow the active design system (`Preset::modalAlignment()`):

- Footer buttons are aligned right, with the primary button last.
- Centered modals (confirmation dialogs, and Tabler's narrow modals) split the buttons evenly across the width.
- shadcn and Ant Design align modal titles to the left.

Actions that set their own `->modalAlignment()` or `->modalFooterActionsAlignment()` keep it. To disable this entirely, use
`->alignModals(false)`.

To set your own default for every action, use `Action::configureUsing(..., isImportant: true)`. Without `isImportant`, your
configuration runs before the plugin and is overridden.

## Colors

If you don't call `->colors()`, the plugin uses the preset's palette. It only fills in colors the panel **hasn't** set, so
your own `->colors()` always wins, whether it's called before or after `->plugin()`.

| Preset | Palette |
|---|---|
| Tabler | primary `#066fd1`, danger `#d63939`, warning `#f59f00`, success `#2fb344`, info `#4299e1`, gray = slate |
| shadcn | primary = neutral scale, near-black `oklch(0.205 0 0)` at shade 600; gray = neutral. In dark mode, the primary button inverts to light gray with black text. |
| Ant Design | The official 10-shade palettes from `@ant-design/colors` (blue, red, gold, green), with the token color at shade 600 and the hover color at 500; gray = antd neutral. Primary and danger buttons keep white text. |

> [!WARNING]
> Ant Design's `#1677ff` on white has a contrast ratio of 3.97:1, below the 4.5:1 that Filament enforces. If you need WCAG AA,
> use `'primary' => Palette::anchored('#0958d9', 600)`.

### Using your exact brand color

Filament's `Color::hex()` only keeps the hue of a color, so your brand color never appears exactly as specified. Use
`Palette` instead:

```php
use FieldVn\FilamentThemes\Support\Palette;

->colors([
    // Generate a scale with this exact color at shade 600 (used by buttons)
    'primary' => Palette::anchored('#0054a6', 600),

    // Or provide a complete 11-shade scale (50–950)
    'info' => Palette::fromShades([50 => '#e6f4ff', /* … */ 950 => '#001d66']),
])
```

## Icons

`->icons()` replaces every Filament icon alias with the preset's icon set (mappings live in `src/Presets/*/Icons.php`):

| Preset | Icon set | Package | Prefix for your own icons |
|---|---|---|---|
| Tabler | Tabler Icons | `secondnetwork/blade-tabler-icons` | `tabler-…` |
| shadcn | Lucide | `mallardduck/blade-lucide-icons` | `lucide-…` |
| Ant Design | Ant Design Icons | `codeat3/blade-ant-design-icons` | `antdesign-…-o` |

- The icon packages are only suggested, not required. If you enable `->icons()` without the right package, the plugin throws
  an error telling you which `composer require` to run.
- Icons you set yourself (such as a resource's navigation icon) are left alone. Use the prefix above to match the preset.
- Ant Design has no sun/moon icons, so the theme switcher keeps Filament's Heroicons.

In production, run `php artisan icons:cache` (or `filament:optimize`) so icon sets aren't scanned on every request.

## Logo

- Light sidebars and the horizontal header use `->brandLogo()`.
- Dark sidebars (`->darkSidebar()`, or Ant Design by default) use `->darkModeBrandLogo()`. If your logo is an image with
  dark text, add a light version with `->darkModeBrandLogo()`.
- Text logos (the brand name) turn white automatically.

## Customizing with CSS variables

Every value comes from a `--ft-*` CSS variable set by the preset, for light mode, `.dark`, and
`html[data-ft-sidebar="dark"]`. Override them in your own Vite theme, or through the `STYLES_AFTER` render hook (registered
**after** the plugin):

```css
:root {
    --ft-sidebar-accent: #d4af37; /* marker on the active item (Tabler's dark sidebar) */
    --ft-table-cell-py: 0.5rem;   /* roomier table rows */
}
```

| Group | Variables |
|---|---|
| Background, border, shadow | `--ft-body-bg`, `--ft-surface`, `--ft-surface-secondary`, `--ft-border`, `--ft-border-input`, `--ft-shadow-card/input/overlay` |
| Focus | `--ft-focus-border`, `--ft-focus-ring`, `--ft-focus-width` |
| Typography | `--ft-font-family`, `--ft-heading-size/weight/…`, `--ft-label-size/weight/transform/…` (small labels: menu groups, breadcrumbs, stats) |
| Spacing | `--ft-topbar-height`, `--ft-page-gap`, `--ft-field-gap`, `--ft-card-padding`, `--ft-card-header-padding`, `--ft-table-cell-py` |
| Table | `--ft-table-head-bg/color/size/weight/transform`, `--ft-table-row-hover` |
| Controls | `--ft-btn-padding/weight/line-height`, `--ft-switch-width/height/knob/shift/off-bg`, `--ft-upload-bg` |
| Overlays | `--ft-overlay` (modal backdrop), `--ft-tooltip-bg/text/size/padding/radius` |
| Sidebar | `--ft-sidebar-bg/text/text-muted/text-active/hover-bg/active-bg/divider/accent`, `--ft-nav-item-padding/radius` |
| Horizontal header | `--ft-navbar-height`; `--ft-container` follows `->maxContentWidth()` |
| Radius | `--radius-sm/md/lg/xl` (Tabler 4/4/6/8px, shadcn derived from `--radius: 0.625rem`, Antd 4/4/6/8px) |

## Component reference

How each component is styled per preset.

### Tables

| Tabler | shadcn (based on `examples/tasks`) | Ant Design (based on ProTable) |
|---|---|---|
| Inside a flat card. Column headers 12px/500 uppercase on light gray. Dark gray cell text. Row actions are text links without icons. | No card, only an 8px rounded border. 32px search input at the top left, filter button with a dashed border. Column headers without background, compact rows, pagination below. | Borderless white card. `#fafafa` column headers with dividers, 16px cell padding. The selection bar is a blue Alert. Row actions are links separated by dividers ("View \| Edit"). |

### Other components

| Component | Tabler | shadcn | Ant Design |
|---|---|---|---|
| Button, input | 40px tall (sm 32, lg 48, xl 56); colored buttons have a dark border and top highlight | 36px tall, px-4 (sm 32, lg 40) | 32px tall (sm 24, lg 40), font weight 400; white buttons turn blue on hover |
| Modal | 56px header with bottom border; light gray footer with top border; Cancel on the far left | 10px radius, 24px padding, 18px title, 8px between buttons | 20px 24px padding, 16px title, 45% backdrop |
| Confirmation dialog | 2px colored bar on top, large icon, full-width buttons | AlertDialog: icon in a tinted rounded square next to the title, buttons on the right | Modal.confirm: 22px icon before the title, indented content, buttons on the right |
| Notification | Toast, 6px radius, soft shadow | Sonner: 13px text, 16px icon, same white card for every type | Notification: 16px title, 20px 24px padding, no border |
| Tooltip | 90% dark background, 12px text | Black, 12px text, 8px radius | 85% black, 14px text |
| Switch | 32×20 | 32×18 | 44×22 |
| Checkbox | 20px, 6px radius | 16px | 16px |
| Dropdown | Full-width items, no radius | Items px-2 py-1.5, 16px icons | Items 5px 12px; delete item turns red on hover |
| Empty state | Large icon, 20px title | Dashed frame, icon in a gray square | Light gray icon, "No data" in secondary text |
| ToggleButtons | Selected: colored border and text on a light tint | Selected: gray `accent` background | Selected: white background, blue border and text (Radio.Button) |
| File upload | Dashed frame | Dashed frame | Dashed frame on `#fafafa`, 8px radius (Upload.Dragger) |
| Wizard | Filament default | Filament default | Steps: 32px circles, current step blue, completed steps light blue |

## How it works

- **Shared structure + per-preset tokens.** `resources/dist/core.css` contains the `fi-*` selectors to override and reads
  only `--ft-*` variables. Each preset file (`tabler.css`, `shadcn.css`, `antd.css`) defines those variables plus a few rules
  of its own (such as shadcn's inset card or antd's column dividers). A page loads only `core.css` and the active preset.
- **Loaded after the panel theme.** The CSS lives in `@layer components` and is injected through the `STYLES_AFTER` render
  hook. Selectors are copied from Filament's own CSS, so they override it while utility classes still win as usual. Button
  colors, which Filament sets with `fi-bg-color-*` utilities, are overridden in `@layer utilities`.
- **Cache-safe file names.** Published files include a content hash (`core-<hash>.css`) instead of a `?v=` query string,
  because CDNs set to ignore query strings (such as Cloudflare's "Ignore query string") would keep serving stale CSS.
- **Non-intrusive defaults.** Colors, icons and fonts are applied in the plugin's `boot()`, after the panel has registered
  its own settings, and only where the panel hasn't set anything.

## Upgrading Filament

If Filament changes a class or its markup, the affected part falls back to Filament's default style. Pages don't break.
After each Filament upgrade:

1. Run `composer test` in this repository (CI also runs it on every push, against the latest Filament). The tests check
   that:
   - all three icon sets still cover every Filament alias, and every icon exists
   - every variable used in `core.css` is defined by all three presets
   - the CSS still loads after the panel theme
   - Tabler's primary button color is still correct
   - the preview page and its modals still open
2. Run `git diff` on `vendor/filament/*/resources/css` between the two versions, and update the matching selectors in
   `core.css` and the preset files.
3. Open the preview page with each preset in light and dark mode, vertical and horizontal layouts, and on mobile.

## Upgrading from filament-tabler-theme

This package was previously named `field-vn/filament-tabler-theme`. Tabler is still the default preset, so the look
doesn't change.

1. Replace the package:

   ```bash
   composer remove field-vn/filament-tabler-theme
   composer require field-vn/filament-themes:^0.1
   ```

2. Rename in your code:

   | Before | After |
   |---|---|
   | `TablerTheme::make()` | `FilamentThemes::make()` (`FieldVn\FilamentThemes\FilamentThemes`) |
   | `->tablerIcons()` | `->icons()` |
   | CSS variables `--tabler-*` | `--ft-*` |
   | `[data-tabler-sidebar]` | `[data-ft-sidebar]` |

3. If you use `->icons()`, install `secondnetwork/blade-tabler-icons`. It is no longer installed with the package.
4. Run `php artisan filament:assets`, then delete the old package's CSS folder under `public/css/field-vn/`.

## Limitations

- Filament's HTML is not changed (no views are published), so designs that need different markup aren't possible, such as
  the logo outside the login card (Tabler) or the page header inside the inset card (shadcn).
- The collapsible sidebar (`sidebarCollapsibleOnDesktop`) is supported, but when collapsed the logo sits on the topbar's
  white background (Ant Design, or when the topbar is enabled).
- shadcn loads Geist from Bunny Fonts (`fonts.bunny.net`). To self-host, set `->font()` on the panel.
- Tooltips are styled through `.tippy-box[data-theme]`, so every tippy.js popup on the page, including those from other
  plugins (such as the tiptap editor menu), also gets the dark style.

## Development

```bash
composer install   # also runs `testbench package:discover`
composer ci        # pint --test, phpstan (level 7), pest
composer format    # pint
```

Tests run on [Orchestra Testbench](https://packages.tools/testbench). `tests/Fixtures/AdminPanelProvider.php` is an `/admin`
panel like the one `filament:install --panels` generates, with the plugin added (`->icons()->previewPage()`). All three icon
sets are in `require-dev` so every preset is tested. CI runs on PHP 8.3/8.4/8.5 × Laravel 12/13 with the latest Filament,
plus one run with the lowest dependency versions `composer.json` allows.

To release: update `CHANGELOG.md`, merge `development` into `main`, then tag `vX.Y.Z` on `main`.

## License

MIT. Developed by [field.vn](https://field.vn/).

The design language follows Tabler, shadcn/ui and Ant Design (all MIT). Icons come from Tabler Icons (MIT), Lucide (ISC)
and Ant Design Icons (MIT). See [`NOTICE`](NOTICE) for details.

## Credits

This package would not exist without the work of these design systems and their authors. All credit for the design
language goes to them:

- **[Tabler](https://tabler.io)** ([GitHub](https://github.com/tabler/tabler)) by Paweł Kuna and the Tabler Authors, MIT
- **[shadcn/ui](https://ui.shadcn.com)** ([GitHub](https://github.com/shadcn-ui/ui)) by shadcn, MIT
- **[Ant Design](https://ant.design)** ([GitHub](https://github.com/ant-design/ant-design)) by Ant UED, MIT

Icons: [Tabler Icons](https://tabler.io/icons) (MIT), [Lucide](https://lucide.dev) (ISC),
[Ant Design Icons](https://ant.design/components/icon) (MIT). Font: [Geist](https://vercel.com/font) (SIL OFL 1.1),
loaded from Bunny Fonts.

This project is not affiliated with, sponsored or endorsed by Tabler, shadcn or Ant Group. Tabler, shadcn/ui and
Ant Design are trademarks of their respective owners, used here only to describe which design system each preset follows.

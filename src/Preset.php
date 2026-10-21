<?php

declare(strict_types=1);

namespace FieldVn\FilamentThemes;

use FieldVn\FilamentThemes\Presets\Antd;
use FieldVn\FilamentThemes\Presets\Shadcn;
use FieldVn\FilamentThemes\Presets\Tabler;
use Filament\Support\Enums\Alignment;

/**
 * A design system: its stylesheet (tokens + a few rules on top of core.css), palette, icon family and layout.
 */
enum Preset: string
{
    /** Tabler (https://tabler.io): light full-height sidebar, no topbar, small uppercase labels. */
    case Tabler = 'tabler';

    /** shadcn/ui (https://ui.shadcn.com): neutral, black primary, content in an inset card next to a muted sidebar. */
    case Shadcn = 'shadcn';

    /** Ant Design Pro (https://ant.design): dark #001529 sider, white header, dense tables, #1677ff. */
    case Antd = 'antd';

    /** Stylesheet name in resources/dist/. */
    public function stylesheet(): string
    {
        return $this->value;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function colors(): array
    {
        return match ($this) {
            self::Tabler => Tabler\Colors::all(),
            self::Shadcn => Shadcn\Colors::all(),
            self::Antd => Antd\Colors::all(),
        };
    }

    /**
     * Filament icon alias => icon of this design system's family.
     *
     * @return array<string, string>
     */
    public function icons(): array
    {
        return match ($this) {
            self::Tabler => Tabler\Icons::map(),
            self::Shadcn => Shadcn\Icons::map(),
            self::Antd => Antd\Icons::map(),
        };
    }

    /**
     * Font family for $panel->font() (Bunny Fonts), when the panel sets none; null keeps Filament's (Inter).
     * Ant Design uses the system font stack, set in antd.css.
     */
    public function font(): ?string
    {
        return match ($this) {
            self::Shadcn => 'Geist',
            self::Tabler, self::Antd => null,
        };
    }

    /** The vertical layout of this design system has a topbar. */
    public function hasTopbar(): bool
    {
        return $this === self::Antd;
    }

    /** The vertical layout of this design system has a dark sidebar. */
    public function hasDarkSidebar(): bool
    {
        return $this === self::Antd;
    }

    /** Sidebar width for $panel->sidebarWidth(); null keeps Filament's. */
    public function sidebarWidth(): ?string
    {
        return match ($this) {
            self::Tabler => null,
            self::Shadcn => '16rem',
            self::Antd => '16rem', // ProLayout siderWidth
        };
    }

    /**
     * Modal alignment for $action->modalAlignment(). Null keeps Filament's: confirmations and small modals centered,
     * like Tabler's modal-danger / modal-sm. Start: shadcn's Dialog / AlertDialog and antd's Modal / Modal.confirm put
     * the title (and the icon before it) on the left, whatever the width.
     */
    public function modalAlignment(): ?Alignment
    {
        return match ($this) {
            self::Tabler => null,
            self::Shadcn, self::Antd => Alignment::Start,
        };
    }

    /** Composer package of the icon family that ->icons() switches to (suggested, not required, by this package). */
    public function iconPackage(): string
    {
        return match ($this) {
            self::Tabler => 'secondnetwork/blade-tabler-icons',
            self::Shadcn => 'mallardduck/blade-lucide-icons',
            self::Antd => 'codeat3/blade-ant-design-icons',
        };
    }
}

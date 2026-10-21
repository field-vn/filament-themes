# Changelog

## v1.0.0 - 2026-10-21

First public release. Projects on the former `field-vn/filament-tabler-theme`: see
[Upgrading from filament-tabler-theme](README.md#upgrading-from-filament-tabler-theme).

- Three presets, chosen with `->preset()`: Tabler (default), shadcn/ui, Ant Design. Each preset has its own palette, icon
  set, font and layout.
- CSS split into `core.css` (structure, reads tokens only) + one file per preset. No build step, light/dark, mobile.
- Per-preset vertical layout (Tabler: white sidebar without topbar; shadcn: inset card; Antd: dark sider + header);
  `->horizontal()` (two-row white header) and `->darkSidebar()` for every preset.
- `Support\Palette::anchored()` keeps the exact brand color at a given shade; `Palette::fromShades()` accepts a ready-made
  11-shade scale.
- Icons: map every Filament 5 icon alias (up to 5.10, including the rich editor toolbar) to Tabler Icons, Lucide, Ant Design
  Icons; glyphs missing from an icon set keep Filament's icon.
- Published CSS files have a content hash in their name, so they stay correct behind CDNs that ignore query strings.
- Tables, callouts, infolist labels and card titles follow each system (measured on Tabler, shadcn and Ant Design's demo
  pages); modal footer buttons are right-aligned.
- Buttons (all 5 sizes), inputs, modals, confirmation dialogs, notifications, tooltips, switches, checkboxes, dropdowns,
  empty states, ToggleButtons and file upload follow each system; Steps-style wizard for Ant Design. Modals are aligned per
  system (`Preset::modalAlignment()`), the footer follows the modal's alignment; `->alignModals(false)` keeps Filament's
  behavior.
- The three icon sets moved to `suggest`: projects install the set of the preset they use; `->icons()` throws a clear error
  if it's missing.
- `->previewPage()`: a "Theme preview" page with common components on sample data.
- Tests (Testbench + Pest), Pint, PHPStan level 7, GitHub Actions (PHP 8.3–8.5 × Laravel 12/13).

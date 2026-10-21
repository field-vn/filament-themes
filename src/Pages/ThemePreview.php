<?php

declare(strict_types=1);

namespace FieldVn\FilamentThemes\Pages;

use BackedEnum;
use FieldVn\FilamentThemes\Widgets\PreviewStats;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\EmptyState;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Every common Filament component on one page, to review a preset (->previewPage()). Sample data only.
 *
 * @property-read Schema $form
 */
final class ThemePreview extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?string $navigationLabel = 'Theme preview';

    protected static ?string $title = 'Theme preview';

    protected static ?string $slug = 'theme-preview';

    protected static ?int $navigationSort = 1000;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    private const array STATUSES = ['active' => 'success', 'pending' => 'warning', 'blocked' => 'danger', 'draft' => 'gray', 'review' => 'info'];

    public function mount(): void
    {
        $this->form->fill([
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'plan' => 'pro',
            'notify' => true,
            'channels' => ['email'],
            'size' => 'md',
            'tags' => ['admin', 'beta'],
            'color' => '#066fd1',
            'meta' => ['region' => 'ap-southeast-1'],
            'members' => [['member' => 'Grace Hopper', 'role' => 'owner']],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Profile')
                    ->description('Text inputs, select, textarea, date')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->required()->helperText('Shown on invoices.'),
                        TextInput::make('email')->email()->prefixIcon(Heroicon::OutlinedEnvelope),
                        TextInput::make('website')->prefix('https://')->suffix('.com')->placeholder('example'),
                        TextInput::make('invalid')->label('Invalid field')->required(),
                        Select::make('plan')->options(['free' => 'Free', 'pro' => 'Pro', 'team' => 'Team'])->native(false),
                        Select::make('country')->options(['vn' => 'Vietnam', 'jp' => 'Japan', 'us' => 'United States'])->searchable(),
                        DatePicker::make('starts_at')->native(false),
                        ColorPicker::make('color'),
                        Textarea::make('bio')->rows(3)->columnSpanFull(),
                        FileUpload::make('attachment')->columnSpanFull(),
                    ]),
                Section::make('Choices')
                    ->columns(3)
                    ->schema([
                        Checkbox::make('terms')->label('Accept terms'),
                        Toggle::make('notify')->label('Email notifications'),
                        Radio::make('size')->options(['sm' => 'Small', 'md' => 'Medium', 'lg' => 'Large'])->inline(),
                        CheckboxList::make('channels')->options(['email' => 'Email', 'sms' => 'SMS', 'push' => 'Push'])->columns(3)->columnSpan(2),
                        ToggleButtons::make('priority')->options(['low' => 'Low', 'normal' => 'Normal', 'high' => 'High'])->inline(),
                        TagsInput::make('tags')->columnSpanFull(),
                    ]),
                Section::make('Nested')
                    ->collapsible()
                    ->schema([
                        Fieldset::make('Metadata')->schema([KeyValue::make('meta')->columnSpanFull()]),
                        Repeater::make('members')->columns(2)->schema([
                            TextInput::make('member'),
                            Select::make('role')->options(['owner' => 'Owner', 'member' => 'Member']),
                        ]),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(1)->schema($this->getWidgetsSchemaComponents([PreviewStats::class])),
            Section::make('Buttons')
                ->description('Colors, outlined, icon, badge, disabled, link, icon button, tooltip, sizes')
                ->schema([
                    Actions::make([
                        Action::make('primaryButton')->label('Primary'),
                        Action::make('grayButton')->label('Secondary')->color('gray'),
                        Action::make('successButton')->label('Success')->color('success'),
                        Action::make('dangerButton')->label('Danger')->color('danger'),
                        Action::make('outlinedButton')->label('Outlined')->outlined(),
                        Action::make('outlinedDangerButton')->label('Outlined danger')->color('danger')->outlined(),
                        Action::make('iconLabelButton')->label('With icon')->icon(Heroicon::OutlinedPlus),
                        Action::make('badgeButton')->label('Inbox')->color('gray')->badge('4'),
                        Action::make('disabledButton')->label('Disabled')->disabled(),
                        Action::make('linkButton')->label('Link')->link(),
                        Action::make('iconButton')->label('Settings')->iconButton()->icon(Heroicon::OutlinedCog6Tooth)->tooltip('Settings'),
                        Action::make('tooltipButton')->label('Hover me')->color('gray')->tooltip('A tooltip'),
                    ]),
                    Actions::make(array_map(
                        fn (Size $size): Action => Action::make("size{$size->name}Button")->label($size->name)->size($size),
                        [Size::ExtraSmall, Size::Small, Size::Medium, Size::Large, Size::ExtraLarge],
                    )),
                ]),
            Callout::make('Heads up')->description('Callouts in info, success, warning and danger.')->info(),
            Grid::make(3)->schema([
                Callout::make('Saved')->description('Your changes are live.')->success(),
                Callout::make('Quota at 90%')->description('Upgrade to keep syncing.')->warning(),
                Callout::make('Sync failed')->description('Check the API key.')->danger(),
            ]),
            EmptyState::make('No invoices yet')
                ->description('Invoices you create will show up here.')
                ->icon(Heroicon::OutlinedDocumentText)
                ->footer([Action::make('createInvoice')->label('New invoice')->icon(Heroicon::OutlinedPlus)]),
            Tabs::make('Sections')->tabs([
                Tab::make('Details')->icon(Heroicon::OutlinedUser)->badge(3)->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('plan')->state('Pro')->badge()->color('success'),
                        TextEntry::make('owner')->state('Grace Hopper'),
                        IconEntry::make('verified')->state(true)->boolean(),
                        TextEntry::make('created')->state('20/10/2026 09:18')->label('Created at'),
                        TextEntry::make('seats')->state('12 of 20')->helperText('8 seats left'),
                        TextEntry::make('statuses')->state(['active', 'pending', 'blocked'])->badge()
                            ->color(fn (string $state): string => self::STATUSES[$state]),
                    ]),
                ]),
                Tab::make('Billing')->icon(Heroicon::OutlinedCreditCard)->schema([
                    TextEntry::make('card')->state('Visa •••• 4242'),
                ]),
                Tab::make('Danger zone')->schema([
                    TextEntry::make('note')->state('Deleting a workspace cannot be undone.'),
                ]),
            ]),
            EmbeddedSchema::make('form'),
            EmbeddedTable::make(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Customers')
            ->description('Static sample rows: search, sort, filters, bulk and row actions, pagination.')
            ->records(fn (?string $search, ?string $sortColumn, ?string $sortDirection, array $filters, int|string $page, int|string $recordsPerPage): LengthAwarePaginator => $this->rows($search, $sortColumn, $sortDirection, $filters['status']['value'] ?? null, (int) $page, (int) $recordsPerPage))
            ->columns([
                TextColumn::make('name')->description(fn (array $record): string => $record['email'])->searchable()->sortable(),
                TextColumn::make('company')->sortable(),
                TextColumn::make('status')->badge()->color(fn (string $state): string => self::STATUSES[$state])->sortable(),
                TextColumn::make('amount')->money('USD')->alignEnd()->sortable(),
                IconColumn::make('verified')->boolean(),
                TextColumn::make('joined')->date()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(array_combine(array_keys(self::STATUSES), array_map(ucfirst(...), array_keys(self::STATUSES)))),
            ])
            ->recordActions([
                Action::make('view')->icon(Heroicon::OutlinedEye)->action(fn (): Notification => Notification::make()->title('Viewed')->send()),
                Action::make('edit')->icon(Heroicon::OutlinedPencilSquare)->action(fn (): Notification => Notification::make()->title('Edited')->send()),
                ActionGroup::make([
                    Action::make('duplicate')->icon(Heroicon::OutlinedSquare2Stack),
                    Action::make('delete')->icon(Heroicon::OutlinedTrash)->color('danger')->requiresConfirmation(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('export')->icon(Heroicon::OutlinedArrowDownTray)->action(fn (): Notification => Notification::make()->title('Exported')->success()->send()),
                    BulkAction::make('archive')->color('danger')->requiresConfirmation(),
                ]),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(10);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('notify')->label('Show notifications')->color('gray')->icon(Heroicon::OutlinedBell)->action(function (): void {
                Notification::make()->title('Saved successfully')->body('Your changes are live.')->success()->send();
                Notification::make()->title('Sync failed')->body('Check the API key and retry.')->danger()->send();
            }),
            Action::make('confirm')->label('Delete')->color('danger')->requiresConfirmation()->modalDescription('This is a confirmation modal.'),
            Action::make('wizard')->label('Wizard')->color('gray')->steps([
                Step::make('Account')->description('Who')->schema([TextInput::make('account_email')->label('Email')->email()]),
                Step::make('Plan')->description('What')->schema([Select::make('account_plan')->label('Plan')->options(['free' => 'Free', 'pro' => 'Pro'])]),
                Step::make('Confirm')->description('Done')->schema([Checkbox::make('account_terms')->label('Accept terms')]),
            ])->action(fn (): Notification => Notification::make()->title('Account created')->success()->send()),
            Action::make('slideOver')->label('Slide-over')->color('gray')->slideOver()->schema([
                TextInput::make('filter_name')->label('Name'),
                Toggle::make('filter_active')->label('Active only'),
            ]),
            Action::make('modal')->label('Open form')->icon(Heroicon::OutlinedPlus)->schema([
                TextInput::make('title')->required(),
                Select::make('type')->options(['a' => 'Type A', 'b' => 'Type B']),
            ])->action(fn (): Notification => Notification::make()->title('Created')->success()->send()),
            ActionGroup::make([
                Action::make('settings')->icon(Heroicon::OutlinedCog6Tooth),
                Action::make('logs')->icon(Heroicon::OutlinedDocumentText),
            ])->button()->label('More')->color('gray'),
        ];
    }

    /**
     * @return LengthAwarePaginator<int, array<string, mixed>>
     */
    private function rows(?string $search, ?string $sortColumn, ?string $sortDirection, ?string $status, int $page, int $perPage): LengthAwarePaginator
    {
        $names = ['Olivia Martin', 'Jackson Lee', 'Isabella Nguyen', 'William Kim', 'Sofia Davis', 'Liam Tran', 'Emma Wilson', 'Noah Pham', 'Ava Brown', 'Lucas Hoang', 'Mia Garcia', 'Ethan Le', 'Charlotte Do', 'Mason Vu', 'Amelia Bui', 'Logan Dang', 'Harper Ngo', 'Elijah Duong', 'Evelyn Ly', 'James Truong', 'Abigail Ho', 'Benjamin Mai', 'Ella Phan'];
        $statuses = array_keys(self::STATUSES);

        /** @var Collection<int, array<string, mixed>> $rows */
        $rows = collect($names)->map(fn (string $name, int $i): array => [
            'id' => $i + 1,
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)).'@example.com',
            'company' => ['Acme', 'Globex', 'Initech', 'Umbrella', 'Stark'][$i % 5].' '.['Inc.', 'Ltd.', 'JSC'][$i % 3],
            'status' => $statuses[$i % count($statuses)],
            'amount' => (($i * 37) % 900 + 100) * 10,
            'verified' => $i % 3 !== 0,
            'joined' => now()->subDays($i * 11)->toDateString(),
        ]);

        if (filled($search)) {
            $rows = $rows->filter(fn (array $row): bool => str_contains(strtolower($row['name'].' '.$row['email']), strtolower($search)));
        }

        if (filled($status)) {
            $rows = $rows->where('status', $status);
        }

        if (filled($sortColumn)) {
            $rows = $rows->sortBy($sortColumn, descending: $sortDirection === 'desc');
        }

        return new LengthAwarePaginator($rows->forPage($page, $perPage)->values(), $rows->count(), $perPage, $page);
    }
}

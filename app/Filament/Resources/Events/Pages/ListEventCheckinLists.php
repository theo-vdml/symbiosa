<?php

namespace App\Filament\Resources\Events\Pages;

use App\Enums\EventNavigationGroups;
use App\Filament\Resources\Events\EventResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ListEventCheckinLists extends ManageRelatedRecords
{
    protected static string $resource = EventResource::class;

    protected static string $relationship = 'checkinLists';

    protected static ?string $navigationLabel = 'Listes de Check-in';

    protected static ?string $breadcrumb = 'Listes de Check-in';

    protected static string|UnitEnum|null $navigationGroup = EventNavigationGroups::Ticketing;

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Listes de Check-in';
    }

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('public_url_token')
                    ->label('Token')
                    ->copyable()
                    ->fontFamily('mono')
                    ->limit(10),
                TextColumn::make('ticket_types_count')
                    ->label('Tickets')
                    ->counts('ticketTypes'),
                TextColumn::make('addons_count')
                    ->label('Extras')
                    ->counts('addons'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->form([
                        TextInput::make('name')
                            ->label('Nom de la liste')
                            ->required()
                            ->placeholder('Ex: Entrée VIP, Bar, etc.'),
                        Select::make('ticketTypes')
                            ->label('Tickets concernés')
                            ->multiple()
                            ->relationship('ticketTypes', 'name', fn($query) => $query->where('event_id', $this->record->id))
                            ->preload(),
                        Select::make('addons')
                            ->label('Extras concernés')
                            ->multiple()
                            ->relationship('addons', 'name', fn($query) => $query->where('event_id', $this->record->id))
                            ->preload(),
                        TextInput::make('public_url_password')
                            ->label('Mot de passe (optionnel)')
                            ->password()
                            ->helperText('Si défini, ce mot de passe sera demandé pour accéder à la liste via l\'URL publique.'),
                    ]),
            ])
            ->actions([
                Action::make('open_public_url')
                    ->label('Ouvrir URL publique')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn ($record) => route('checkin.public', ['token' => $record->public_url_token]))
                    ->openUrlInNewTab(),
                EditAction::make()
                    ->form([
                        TextInput::make('name')
                            ->label('Nom de la liste')
                            ->required(),
                        Select::make('ticketTypes')
                            ->label('Tickets concernés')
                            ->multiple()
                            ->relationship('ticketTypes', 'name', fn($query) => $query->where('event_id', $this->record->id))
                            ->preload(),
                        Select::make('addons')
                            ->label('Extras concernés')
                            ->multiple()
                            ->relationship('addons', 'name', fn($query) => $query->where('event_id', $this->record->id))
                            ->preload(),
                        TextInput::make('public_url_password')
                            ->label('Mot de passe (optionnel)')
                            ->password()
                            ->helperText('Laissez vide pour ne pas changer. Effacez pour supprimer le mot de passe.')
                            ->dehydrated(fn ($state) => filled($state)),
                    ]),
                DeleteAction::make(),
            ]);
    }
}

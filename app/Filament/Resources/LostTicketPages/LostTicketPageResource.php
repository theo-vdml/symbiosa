<?php

namespace App\Filament\Resources\LostTicketPages;

use App\Enums\NavigationGroups;
use App\Filament\Shared\Schemas\SeoSchema;
use App\Models\LostTicketPage;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class LostTicketPageResource extends Resource
{
    protected static ?string $model = LostTicketPage::class;

    protected static UnitEnum|string|null $navigationGroup = NavigationGroups::Pages;
    protected static BackedEnum|string|null $navigationIcon = Heroicon::Ticket;
    protected static ?string $navigationLabel = "Billets perdus";
    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contenu de la page')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('preheading')
                            ->label('Pré-titre')
                            ->maxLength(255),
                        TextInput::make('heading')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3),
                    ]),

                Section::make('Aide')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('help_items')
                            ->label('Éléments d\'aide')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Titre')
                                    ->required(),
                                Textarea::make('content')
                                    ->label('Contenu')
                                    ->required()
                                    ->rows(3),
                            ])
                            ->itemLabel(fn(array $state): ?string => $state['title'] ?? null)
                            ->collapsible(),
                    ]),

                SeoSchema::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLostTicketPages::route('/'),
            'create' => Pages\CreateLostTicketPage::route('/create'),
            'edit' => Pages\EditLostTicketPage::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return LostTicketPage::count() === 0;
    }

    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false, ?string $configuration = null): string
    {
        if ($name === 'index' || $name === null) {
            $record = LostTicketPage::first();
            if ($record) {
                return parent::getUrl('edit', ['record' => $record], $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
            }
            return parent::getUrl('create', [], $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
        }

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
    }
}

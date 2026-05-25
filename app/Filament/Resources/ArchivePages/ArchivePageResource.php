<?php

namespace App\Filament\Resources\ArchivePages;

use App\Enums\NavigationGroups;
use App\Filament\Shared\Schemas\SeoSchema;
use App\Models\ArchivePage;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ArchivePageResource extends Resource
{
    protected static ?string $model = ArchivePage::class;

    protected static UnitEnum|string|null $navigationGroup = NavigationGroups::Pages;
    protected static BackedEnum|string|null $navigationIcon = Heroicon::ArchiveBox;
    protected static ?string $navigationLabel = "Archives";
    protected static ?int $navigationSort = 5;

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

                SeoSchema::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArchivePages::route('/'),
            'create' => Pages\CreateArchivePage::route('/create'),
            'edit' => Pages\EditArchivePage::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return ArchivePage::count() === 0;
    }

    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false, ?string $configuration = null): string
    {
        if ($name === 'index' || $name === null) {
            $record = ArchivePage::first();
            if ($record) {
                return parent::getUrl('edit', ['record' => $record], $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
            }
            return parent::getUrl('create', [], $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
        }

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
    }
}

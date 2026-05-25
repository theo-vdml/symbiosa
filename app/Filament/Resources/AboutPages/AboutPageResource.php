<?php

namespace App\Filament\Resources\AboutPages;

use App\Enums\NavigationGroups;
use App\Filament\Resources\AboutPages\Pages\CreateAboutPage;
use App\Filament\Resources\AboutPages\Pages\EditAboutPage;
use App\Filament\Resources\AboutPages\Pages\ListAboutPages;
use App\Filament\Shared\Schemas\SeoSchema;
use App\Models\AboutPage;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AboutPageResource extends Resource
{
    protected static ?string $model = AboutPage::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroups::Pages;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;
    protected static ?string $navigationLabel = "À propos";
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sections de la page')
                    ->columnSpanFull()
                    ->description('Ajoutez, modifiez ou supprimez les sections de la page "À propos".')
                    ->schema([
                        Repeater::make('sections')
                            ->relationship('sections')
                            ->hiddenLabel()
                            ->addActionLabel('Ajouter une section')
                            ->addBetweenActionLabel('Ajouter une section')
                            ->reorderableWithButtons()
                            ->orderColumn('sort_order')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Group::make([
                                            TextInput::make('title')
                                                ->label('Titre de la section')
                                                ->placeholder('Ex: Notre histoire')
                                                ->maxLength(255)
                                                ->required(),
                                            Textarea::make('content')
                                                ->label('Contenu de la section')
                                                ->autosize()
                                                ->placeholder('Rédigez un petit peu de contenu pour parler de l\'ASBL ...')
                                                ->rows(5)
                                                ->required(),
                                        ])->columnSpan(2),
                                        SpatieMediaLibraryFileUpload::make('image')
                                            ->label('Image de la section')
                                            ->collection('image')
                                            ->image()
                                            ->required()
                                            ->optimize('webp'),
                                    ])
                            ])
                    ]),

                SeoSchema::make()
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAboutPages::route('/'),
            'create' => CreateAboutPage::route('/create'),
            'edit' => EditAboutPage::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return AboutPage::count() === 0;
    }

    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false, ?string $configuration = null): string
    {
        if ($name === 'index' || $name === null) {
            $record = AboutPage::first();
            if ($record) {
                return parent::getUrl('edit', ['record' => $record], $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
            }
            return parent::getUrl('create', [], $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
        }

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
    }
}

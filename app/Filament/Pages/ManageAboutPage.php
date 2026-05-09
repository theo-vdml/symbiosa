<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroups;
use App\Settings\AboutPageSettings;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageAboutPage extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;
    protected static ?string $navigationLabel = "À propos";
    protected static string|UnitEnum|null $navigationGroup = NavigationGroups::Pages;
    protected static ?int $navigationSort = 2;
    protected ?string $heading = "À propos";
    protected ?string $subheading = "Modifier la page 'À propos' du site";

    protected static string $settings = AboutPageSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sections de la page')
                    ->columnSpanFull()
                    ->description('Ajoutez, modifiez ou supprimez les sections de la page "À propos".')
                    ->schema([
                        Repeater::make('sections')
                            ->hiddenLabel()
                            ->addActionLabel('Ajouter une section')
                            ->addBetweenActionLabel('Ajouter une section')
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
                                        FileUpload::make('image')
                                            ->label('Image de la section')
                                            ->image()
                                            ->disk('public')
                                            ->visibility('public')
                                            ->directory('about/sections')
                                            ->imageEditor()
                                            ->imagePreviewHeight(300)
                                            ->required(),
                                    ])
                            ])
                    ])
            ]);
    }
}

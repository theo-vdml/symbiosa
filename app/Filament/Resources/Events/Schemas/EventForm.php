<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->contained(false)
                    ->tabs([
                        Tab::make('Présentation')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Titre')
                                    ->required()
                                    ->columnSpanFull(),

                                RichEditor::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Details')
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                Section::make('Date')
                                    ->icon('heroicon-o-calendar')
                                    ->description('Date et heure de l\'événement')
                                    ->collapsible()
                                    ->schema([
                                        DatePicker::make('date')
                                            ->label('Date')
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('l j F Y'),

                                        Grid::make(2)
                                            ->schema([
                                                TimePicker::make('start_time')
                                                    ->label('Heure de début')
                                                    ->required()
                                                    ->native(false)
                                                    ->seconds(false)
                                                    ->displayFormat('H:i'),

                                                TimePicker::make('end_time')
                                                    ->label('Heure de fin')
                                                    ->required()
                                                    ->native(false)
                                                    ->seconds(false)
                                                    ->displayFormat('H:i'),
                                            ]),
                                    ]),

                                Section::make('Lieu')
                                    ->icon(Heroicon::MapPin)
                                    ->description('Informations sur le lieu de l\'événement')
                                    ->collapsible()
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('city')
                                                    ->label('Ville')
                                                    ->required(),

                                                TextInput::make('country')
                                                    ->label('Pays')
                                                    ->required(),
                                            ]),

                                        Textarea::make('address')
                                            ->label('Adresse complète')
                                            ->required()
                                            ->rows(4),
                                    ]),

                                Section::make('Autre')
                                    ->icon('heroicon-o-ellipsis-horizontal')
                                    ->description('Informations complémentaires')
                                    ->collapsible()
                                    ->schema([
                                        TextInput::make('dress_code')
                                            ->label('Dress code'),

                                        TextInput::make('minimum_age')
                                            ->label('Âge minimum')
                                            ->numeric(),
                                    ]),
                            ]),

                        Tab::make('Visuels')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        FileUpload::make('poster')
                                            ->label('Poster')
                                            ->image()
                                            ->required()
                                            ->directory('events/posters')
                                            ->imageEditor(),

                                        FileUpload::make('background')
                                            ->label('Background')
                                            ->image()
                                            ->directory('events/backgrounds')
                                            ->imageEditor(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}

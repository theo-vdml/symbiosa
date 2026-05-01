<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
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
                            ->schema([
                                Section::make('Informations générales')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Titre')
                                            ->prefixIcon(Heroicon::PencilSquare)
                                            ->placeholder('Donnez un nom à l\'événement')
                                            ->required()
                                            ->columnSpanFull(),

                                        RichEditor::make('description')
                                            ->label('Description')
                                            ->placeholder('Décrivez l\'événement')
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Details')
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                Section::make('Date et heure')
                                    ->description('Date et heure de l\'événement')
                                    ->collapsible()
                                    ->schema([
                                        DatePicker::make('date')
                                            ->label('Date')
                                            ->prefixIcon(Heroicon::CalendarDays)
                                            ->placeholder('Sélectionnez la date de l\'événement')
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('l j F Y'),

                                        Grid::make(2)
                                            ->schema([
                                                TimePicker::make('start_time')
                                                    ->label('Heure de début')
                                                    ->prefixIcon(Heroicon::Clock)
                                                    ->placeholder('Sélectionnez l\'heure de début')
                                                    ->required()
                                                    ->native(false)
                                                    ->seconds(false)
                                                    ->displayFormat('H:i'),

                                                TimePicker::make('end_time')
                                                    ->prefixIcon(Heroicon::Clock)
                                                    ->label('Heure de fin')
                                                    ->placeholder('Sélectionnez l\'heure de fin')
                                                    ->required()
                                                    ->native(false)
                                                    ->seconds(false)
                                                    ->displayFormat('H:i'),
                                            ]),
                                    ]),

                                Section::make('Lieu')
                                    ->description('Informations sur le lieu de l\'événement')
                                    ->collapsible()
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('city')
                                                    ->label('Ville')
                                                    ->prefixIcon(Heroicon::BuildingStorefront)
                                                    ->placeholder('Entrez la ville où se déroule l\'événement')
                                                    ->required(),

                                                TextInput::make('country')
                                                    ->label('Pays')
                                                    ->prefixIcon(Heroicon::GlobeEuropeAfrica)
                                                    ->placeholder('Entrez le pays où se déroule l\'événement')
                                                    ->required(),
                                            ]),

                                        TextInput::make('address')
                                            ->label('Adresse complète')
                                            ->prefixIcon(Heroicon::MapPin)
                                            ->placeholder('Entrez l\'adresse complète du lieu de l\'événement')
                                            ->required(),
                                    ]),

                                Section::make('Autre')
                                    ->icon('heroicon-o-ellipsis-horizontal')
                                    ->description('Informations complémentaires')
                                    ->collapsible()
                                    ->schema([
                                        TextInput::make('dress_code')
                                            ->label('Dress code')
                                            ->prefixIcon(Heroicon::Sparkles)
                                            ->placeholder('Indiquez le dress code de l\'événement')
                                            ->suffixAction(
                                                Action::make('no_dress_code')
                                                    ->label('Pas de dress code')
                                                    ->icon(Heroicon::XMark)
                                                    ->action(fn(TextInput $component) => $component->state(null))
                                                    ->disabled(fn(TextInput $component) => $component->getState() === null)
                                            )
                                            ->live(),

                                        TextInput::make('minimum_age')
                                            ->label('Âge minimum')
                                            ->prefixIcon(Heroicon::Cake)
                                            ->placeholder('Indiquez l\'âge minimum requis')
                                            ->numeric()
                                            ->integer()
                                            ->hintAction(
                                                Action::make('18_plus')
                                                    ->label('18 ans et plus')
                                                    ->action(fn(TextInput $component) => $component->state(18))
                                                    ->visible(fn(TextInput $component) => $component->getState() !== 18.00)
                                            )
                                            ->suffixAction(
                                                Action::make('no_age_limit')
                                                    ->label('Pas de limite d\'âge')
                                                    ->icon(Heroicon::XMark)
                                                    ->action(fn(TextInput $component) => $component->state(null))
                                                    ->disabled(fn(TextInput $component) => $component->getState() === null)
                                            )
                                            ->live(),
                                    ]),
                            ]),

                        Tab::make('Médias')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Visuels de l\'événement')
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
                                    ])
                            ]),

                        Tab::make('FAQ')
                            ->icon('heroicon-o-question-mark-circle')
                            ->schema([
                                Section::make('Foire aux questions')
                                    ->description('Ajoutez des questions fréquentes pour aider les participants')
                                    ->schema([
                                        Repeater::make('faq')
                                            ->hiddenLabel()
                                            ->addActionLabel('Ajouter une question')
                                            ->deleteAction(
                                                fn(Action $action) => $action->requiresConfirmation(),
                                            )
                                            ->schema([
                                                TextInput::make('question')
                                                    ->label('Question')
                                                    ->placeholder('Entrez une question fréquente')
                                                    ->required()
                                                    ->prefixIcon(Heroicon::QuestionMarkCircle),

                                                Textarea::make('answer')
                                                    ->label('Réponse')
                                                    ->placeholder('Entrez la réponse à cette question')
                                                    ->autosize()
                                                    ->rows(1)
                                                    ->required()
                                            ])
                                    ])
                            ])

                    ]),
            ]);
    }
}

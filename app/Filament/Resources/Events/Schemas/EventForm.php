<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Models\Sponsor;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Utilities\Set;
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
                            ->schema(static::getBasicInfoSchema()),

                        Tab::make('Details')
                            ->icon('heroicon-o-calendar')
                            ->schema(static::getDetailsSchema()),

                        Tab::make('Médias')
                            ->icon('heroicon-o-photo')
                            ->schema(static::getVisualsSchema()),

                        Tab::make('FAQ')
                            ->icon('heroicon-o-question-mark-circle')
                            ->schema(static::getFaqSchema()),

                        Tab::make('Lineup')
                            ->icon('heroicon-o-users')
                            ->schema(static::getLineupSchema()),

                        Tab::make('Sponsors')
                            ->icon(Heroicon::Heart)
                            ->schema(static::getSponsorsSchema())

                    ]),
            ]);
    }

    public static function getBasicInfoSchema(): array
    {
        return [
            Section::make('Informations générales')
                ->columnSpanFull()
                ->schema([
                    TextInput::make('title')
                        ->label('Titre')
                        ->prefixIcon(Heroicon::PencilSquare)
                        ->placeholder('Donnez un nom à l\'événement')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state))),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->prefixIcon(Heroicon::Link)
                        ->placeholder('Identifiant unique pour l\'URL')
                        ->required()
                        ->unique('events', 'slug', ignoreRecord: true),

                    Textarea::make('description')
                        ->label('Description')
                        ->autosize()
                        ->rows(3)
                        ->placeholder('Décrivez l\'événement en quelques mots')
                        ->required()
                        ->columnSpanFull(),
                ]),
        ];
    }

    public static function getDateTimeSchema(bool $collapsible = false): array
    {
        return [
            Section::make('Date et heure')
                ->description('Date et heure de l\'événement')
                ->collapsible($collapsible)
                ->columnSpanFull()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            DateTimePicker::make('start_at')
                                ->label('Début')
                                ->prefixIcon(Heroicon::CalendarDays)
                                ->placeholder('Sélectionnez la date et l\'heure de début')
                                ->required()
                                ->native(false)
                                ->displayFormat('l j F Y H:i'),

                            DateTimePicker::make('end_at')
                                ->label('Fin')
                                ->prefixIcon(Heroicon::Clock)
                                ->placeholder('Sélectionnez la date et l\'heure de fin')
                                ->required()
                                ->native(false)
                                ->displayFormat('l j F Y H:i'),
                        ]),
                ]),
        ];
    }

    public static function getLocationSchema(bool $collapsible = false): array
    {
        return [
            Section::make('Lieu')
                ->description('Informations sur le lieu de l\'événement')
                ->collapsible($collapsible)
                ->columnSpanFull()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('city')
                                ->label('Ville')
                                ->prefixIcon(Heroicon::BuildingStorefront)
                                ->placeholder('Entrez la ville où se déroule l\'événement')
                                ->hintAction(
                                    Action::make('gembloux_shortcut')
                                        ->label('Gembloux')
                                        ->action(fn(TextInput $component) => $component->state('Gembloux'))
                                        ->visible(fn(TextInput $component) => $component->getState() !== 'Gembloux')
                                )
                                ->required(),

                            TextInput::make('country')
                                ->label('Pays')
                                ->prefixIcon(Heroicon::GlobeEuropeAfrica)
                                ->placeholder('Entrez le pays où se déroule l\'événement')
                                ->hintAction(
                                    Action::make('belgium_shortcut')
                                        ->label('Belgique')
                                        ->action(fn(TextInput $component) => $component->state('Belgique'))
                                        ->visible(fn(TextInput $component) => $component->getState() !== 'Belgique')
                                )
                                ->required(),
                        ]),

                    TextInput::make('address')
                        ->label('Adresse complète')
                        ->prefixIcon(Heroicon::MapPin)
                        ->placeholder('Entrez l\'adresse complète du lieu de l\'événement')
                ]),
        ];
    }

    public static function getCopywrittingSchema(): array
    {
        return [
            Section::make('Copywritting')
                ->description('Rédigez des textes accrocheurs pour promouvoir l\'événement')
                ->columnSpanFull()
                ->schema([
                    RichEditor::make('body')
                        ->label('Contenu de la page')
                        ->placeholder('Rédigez le contenu de la page de l\'événement avec des détails, des anecdotes, etc.')
                        ->required()
                        ->columnSpanFull(),
                ]),
        ];
    }

    public static function getDetailsSchema(): array
    {
        return [
            Section::make('Genres musicaux')
                ->description('Sélectionnez les genres musicaux de l\'événement')
                ->columnSpanFull()
                ->schema([
                    Select::make('genres')
                        ->label('Genres')
                        ->placeholder('Sélectionnez les genres musicaux associés à cet événement')
                        ->prefixIcon(Heroicon::MusicalNote)
                        ->multiple()
                        ->relationship('genres', 'name')
                        ->preload()
                        ->searchable()
                        ->quickAdd(label: "Nouveau genre: {search}", resetSearch: true),
                ]),

            Section::make('Autre')
                ->icon('heroicon-o-ellipsis-horizontal')
                ->description('Informations complémentaires')
                ->columnSpanFull()
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
        ];
    }

    public static function getVisualsSchema(): array
    {
        return [
            Section::make('Visuels de l\'événement')
                ->columnSpanFull()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('poster')
                                ->label('Poster')
                                ->collection('poster')
                                ->disk('r2')
                                ->visibility('public')
                                ->image()
                                ->imageAspectRatio('3:4')
                                ->automaticallyOpenImageEditorForAspectRatio()
                                ->automaticallyCropImagesToAspectRatio()
                                ->imageEditor()
                                ->optimize('webp'),

                            SpatieMediaLibraryFileUpload::make('background')
                                ->label('Background')
                                ->collection('background')
                                ->disk('r2')
                                ->visibility('public')
                                ->image()
                                ->imageAspectRatio('16:9')
                                ->automaticallyOpenImageEditorForAspectRatio()
                                ->automaticallyCropImagesToAspectRatio()
                                ->imageEditor()
                                ->optimize('webp'),
                        ]),
                ])
        ];
    }

    public static function getFaqSchema(): array
    {
        return [
            Section::make('Foire aux questions')
                ->description('Ajoutez des questions fréquentes pour aider les participants')
                ->columnSpanFull()
                ->schema([
                    Repeater::make('faq')
                        ->hiddenLabel()
                        ->addActionLabel('Ajouter une question')
                        ->defaultItems(0)
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
        ];
    }

    public static function getSponsorsSchema(): array
    {
        return [
            Section::make('Sponsors de l\'événement')
                ->description('Gérez les sponsors associés à cet événement')
                ->columnSpanFull()
                ->schema([
                    Repeater::make('eventSponsors')
                        ->hiddenLabel()
                        ->relationship()
                        ->addActionLabel('Ajouter un sponsor')
                        ->schema([
                            ViewField::make('sponsor_id')
                                ->label('Aperçu du sponsor')
                                ->view('filament.forms.components.sponsor-preview')
                                ->columnSpanFull(),

                            Select::make('sponsor_id')
                                ->hiddenLabel()
                                ->relationship('sponsor', 'name')
                                ->preload()
                                ->placeholder('Sélectionnez un sponsor')
                                ->searchable()
                                ->selectablePlaceholder(false)
                                ->required()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->prefixIcon('heroicon-m-briefcase')
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->label('Nom du sponsor')
                                        ->required(),
                                    SpatieMediaLibraryFileUpload::make('logo')
                                        ->label('Logo du sponsor')
                                        ->collection('logo')
                                        ->disk('r2')
                                        ->visibility('public')
                                        ->image()
                                        ->automaticallyResizeImagesMode('cover')
                                        ->automaticallyResizeImagesToWidth('800')
                                        ->required(),
                                    TextInput::make('website')
                                        ->label('Site web')
                                        ->url()
                                        ->prefixIcon('heroicon-m-globe-alt'),
                                    Textarea::make('description')
                                        ->label('Description')
                                        ->autosize()
                                        ->columnSpanFull(),
                                ])
                                ->editOptionForm([
                                    TextInput::make('name')
                                        ->label('Nom du sponsor')
                                        ->required(),
                                    SpatieMediaLibraryFileUpload::make('logo')
                                        ->label('Logo du sponsor')
                                        ->collection('logo')
                                        ->disk('r2')
                                        ->visibility('public')
                                        ->image()
                                        ->automaticallyResizeImagesMode('cover')
                                        ->automaticallyResizeImagesToWidth('800')
                                        ->required(),
                                    TextInput::make('website')
                                        ->label('Site web')
                                        ->url()
                                        ->prefixIcon('heroicon-m-globe-alt'),
                                    Textarea::make('description')
                                        ->label('Description')
                                        ->autosize()
                                        ->columnSpanFull(),
                                ])
                                ->live(),
                        ])
                        ->itemLabel(
                            fn(array $state): ?string =>
                            isset($state['sponsor_id'])
                                ? Sponsor::find($state['sponsor_id'])?->name
                                : 'Nouveau Sponsor'
                        )
                        ->deleteAction(
                            fn(Action $action) => $action->requiresConfirmation(),
                        )
                        ->defaultItems(0)
                        ->reorderable()
                        ->orderColumn('sort_order')
                        ->grid(2)
                ])
        ];
    }

    public static function getLineupSchema(): array
    {
        return [
            Section::make('Lineup de l\'événement')
                ->description('Gérez les artistes associés à cet événement')
                ->columnSpanFull()
                ->schema([
                    Repeater::make('artistEvents')
                        ->hiddenLabel()
                        ->relationship()
                        ->addActionLabel('Ajouter un artiste')
                        ->schema([
                            ViewField::make('artist_id')
                                ->label('Aperçu de l\'artiste')
                                ->view('filament.forms.components.artist-preview')
                                ->columnSpanFull(),

                            Select::make('artist_id')
                                ->label('Artiste')
                                ->relationship('artist', 'name')
                                ->preload()
                                ->placeholder('Sélectionnez un artiste')
                                ->searchable()
                                ->selectablePlaceholder(false)
                                ->required()
                                ->prefixIcon(Heroicon::User)
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->createOptionForm(static::getArtistFormSchema())
                                ->editOptionForm(static::getArtistFormSchema())
                                ->live(),

                            TimePicker::make('performance_time')
                                ->label('Heure de passage')
                                ->prefixIcon(Heroicon::Clock)
                                ->native(false)
                                ->seconds(false)
                                ->displayFormat('H:i')
                                ->live(),
                        ])
                        ->itemLabel(
                            fn(array $state): ?string =>
                            isset($state['artist_id'])
                                ? \App\Models\Artist::find($state['artist_id'])?->name
                                : 'Nouvel Artiste'
                        )
                        ->defaultItems(0)
                        ->reorderable()
                        ->orderColumn('sort_order')
                        ->deleteAction(
                            fn(Action $action) => $action->requiresConfirmation(),
                        )
                        ->grid([
                            'md' => 1,
                            'xl' => 2,
                        ])
                ])
        ];
    }

    public static function getSeoSchema(): array
    {
        return [
            \App\Filament\Shared\Schemas\SeoSchema::make(),
        ];
    }

    public static function getArchivesSchema(): array
    {
        return [
            Section::make('Archives')
                ->description('Paramètres de visibilité dans les archives et galerie photo post-événement.')
                ->columnSpanFull()
                ->schema([
                    Toggle::make('is_visible_in_archives')
                        ->label('Visible dans les archives')
                        ->helperText('Si activé, l\'événement apparaîtra dans la page des archives et le mode "Archive" sera activé sur sa page de détail.')
                        ->columnSpanFull(),

                    SpatieMediaLibraryFileUpload::make('gallery')
                        ->label('Galerie photo')
                        ->collection('gallery')
                        ->disk('r2')
                        ->multiple()
                        ->appendFiles()
                        ->reorderable()
                        ->visibility('public')
                        ->image()
                        ->imageEditor()
                        ->columnSpanFull()
                        ->optimize('webp', 100),
                ])
        ];
    }

    public static function getArtistFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nom de l\'artiste')
                ->required(),
            SpatieMediaLibraryFileUpload::make('portrait')
                ->label('Portrait de l\'artiste')
                ->collection('portrait')
                ->disk('r2')
                ->visibility('public')
                ->image()
                ->automaticallyResizeImagesToWidth('1080')
                ->required()
                ->optimize('webp', 85),
            TextInput::make('website')
                ->label('Site web / Instagram')
                ->url()
                ->prefixIcon('heroicon-m-globe-alt'),
            Select::make('genres')
                ->label('Genres')
                ->multiple()
                ->relationship('genres', 'name')
                ->preload()
                ->searchable()
                ->quickAdd(label: "Nouveau genre: {search}", resetSearch: true),
            Textarea::make('biography')
                ->label('Biographie')
                ->columnSpanFull(),
        ];
    }
}

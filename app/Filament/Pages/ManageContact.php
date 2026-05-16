<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroups;
use App\Settings\ContactSettings;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageContact extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;
    protected static ?string $navigationLabel = "Contact";
    protected static string|UnitEnum|null $navigationGroup = NavigationGroups::Pages;
    protected static ?int $navigationSort = 3;
    protected ?string $heading = "Contact";
    protected ?string $subheading = "Modifier la page de contact du site";

    protected static string $settings = ContactSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('En-tête de la page')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('heading')
                            ->label('Titre')
                            ->placeholder('Ex: Contact')
                            ->prefixIcon(Heroicon::H1)
                            ->required(),
                        TextInput::make('subheading')
                            ->label('Sous-titre')
                            ->placeholder('Ex: Nous contacter')
                            ->prefixIcon(Heroicon::Hashtag)
                            ->required(),
                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Une petite phrase d\'introduction pour la page...')
                            ->rows(3)
                            ->autosize()
                            ->required(),
                    ]),

                Section::make('FAQ')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('faq_heading')
                            ->label('Titre FAQ')
                            ->placeholder('Ex: Questions Fréquentes')
                            ->prefixIcon(Heroicon::QuestionMarkCircle)
                            ->required(),
                        Textarea::make('faq_description')
                            ->label('Description FAQ')
                            ->placeholder('Une courte phrase pour introduire la section FAQ...')
                            ->rows(2)
                            ->autosize()
                            ->required(),
                        Repeater::make('faq_items')
                            ->label('Questions / Réponses')
                            ->schema([
                                TextInput::make('question')
                                    ->label('Question')
                                    ->placeholder('Ex: Comment récupérer mes billets ?')
                                    ->prefixIcon(Heroicon::ChatBubbleLeftRight)
                                    ->required(),
                                Textarea::make('answer')
                                    ->label('Réponse')
                                    ->placeholder('Rédigez la réponse ici...')
                                    ->required()
                                    ->rows(4)
                                    ->autosize(),
                            ])
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['question'] ?? null),
                    ]),

                Section::make('Options de contact (Email)')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('email_heading')
                            ->label('Titre de la section e-mail')
                            ->placeholder('Ex: Nous contacter par e-mail')
                            ->prefixIcon(Heroicon::AtSymbol)
                            ->required(),
                        Repeater::make('email_options')
                            ->label('Emails')
                            ->schema([
                                TextInput::make('label')
                                    ->label('Libellé')
                                    ->placeholder('Ex: Une question ?')
                                    ->prefixIcon(Heroicon::Tag)
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Adresse e-mail')
                                    ->placeholder('Ex: hi@symbiosa.be')
                                    ->prefixIcon(Heroicon::Envelope)
                                    ->email()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['label'] ?? null),
                    ]),

                \App\Filament\Shared\Schemas\SeoSchema::make(withRelationship: false, prefix: 'seo')
                    ->columnSpanFull(),
            ]);
    }
}

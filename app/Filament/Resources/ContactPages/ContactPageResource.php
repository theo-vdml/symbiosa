<?php

namespace App\Filament\Resources\ContactPages;

use App\Enums\NavigationGroups;
use App\Filament\Resources\ContactPages\Pages\CreateContactPage;
use App\Filament\Resources\ContactPages\Pages\EditContactPage;
use App\Filament\Resources\ContactPages\Pages\ListContactPages;
use App\Filament\Shared\Schemas\SeoSchema;
use App\Models\ContactPage;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ContactPageResource extends Resource
{
    protected static ?string $model = ContactPage::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroups::Pages;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;
    protected static ?string $navigationLabel = "Contact";
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
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

                Section::make('Section E-mail')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('email_heading')
                            ->label('Titre de la section e-mail')
                            ->placeholder('Ex: Nous contacter par e-mail')
                            ->prefixIcon(Heroicon::AtSymbol)
                            ->required(),
                    ]),

                SeoSchema::make()
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactPages::route('/'),
            'create' => CreateContactPage::route('/create'),
            'edit' => EditContactPage::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return ContactPage::count() === 0;
    }

    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false, ?string $configuration = null): string
    {
        if ($name === 'index' || $name === null) {
            $record = ContactPage::first();
            if ($record) {
                return parent::getUrl('edit', ['record' => $record], $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
            }
            return parent::getUrl('create', [], $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
        }

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
    }
}

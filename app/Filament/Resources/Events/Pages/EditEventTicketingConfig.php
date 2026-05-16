<?php

namespace App\Filament\Resources\Events\Pages;

use App\Enums\EventNavigationGroups;
use App\Filament\Resources\Events\EventResource;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class EditEventTicketingConfig extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static ?string $navigationLabel = 'Configuration Billetterie';

    protected static string|UnitEnum|null $navigationGroup = EventNavigationGroups::Ticketing;

    protected static ?string $breadcrumb = 'Configuration';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Configuration Billetterie';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Callout::make('ticketing_config_warning')
                    ->columnSpanFull()
                    ->warning()
                    ->heading('Action requise : module de billetterie désactivé')
                    ->description('Définissez une date d\'ouverture pour activer le module de billetterie. Tant que cette date n\'est pas renseignée, les options d\'achat de billets ne seront pas visibles par les utilisateurs.')
                    ->visible(fn() => !$this->record->ticketing_starts_at),

                Section::make('Disponibilité de la billetterie')
                    ->columnSpanFull()
                    ->description('Définissez les dates d\'ouverture et de fermeture de la billetterie.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('ticketing_starts_at')
                                    ->prefixIcon(Heroicon::OutlinedCalendar)
                                    ->label('Ouverture de la billetterie')
                                    ->placeholder('Sélectionnez une date et une heure')
                                    ->native(false)
                                    ->displayFormat('d/m/Y H:i')
                                    ->seconds(false)
                                    ->belowContent('La billetterie ouvrira automatiquement à cette date. Ce champs est requis pour activer le module de billetterie. Autrement la billetterie restera fermée et les billets ne seront pas visibles par les utilisateurs.'),
                                DateTimePicker::make('ticketing_ends_at')
                                    ->prefixIcon(Heroicon::OutlinedCalendar)
                                    ->label('Fermeture de la billetterie')
                                    ->placeholder('Sélectionnez une date et une heure')
                                    ->native(false)
                                    ->displayFormat('d/m/Y H:i')
                                    ->seconds(false)
                                    ->after('ticketing_starts_at')
                                    ->belowContent('La billetterie fermera automatiquement à cette date, même si les billets ne sont pas tous vendus. Par défaut la billetterie restera ouverte jusqu\'à la date de l\'événement.'),
                            ]),
                    ]),

                Section::make('Contenu personnalisé')
                    ->columnSpanFull()
                    ->description('Personnalisez les messages envoyés aux clients.')
                    ->schema([
                        RichEditor::make('ticket_email_content')
                            ->label('Contenu du mail d\'envoi des billets')
                            ->helperText('Ce contenu sera ajouté au mail de confirmation de commande.')
                            ->placeholder('Commencez à rediger le contenu du mail ...')
                            ->columnSpanFull(),
                        RichEditor::make('ticket_pdf_content')
                            ->label('Contenu additionnel du PDF')
                            ->helperText('Ce contenu sera ajouté sur une page dédiée à la fin du PDF des billets.')
                            ->placeholder('Commencez à rediger le contenu du document ...')
                            ->columnSpanFull(),
                    ]),

                Section::make('Intégration Stripe (Développeurs)')
                    ->icon(Heroicon::CodeBracket)
                    ->collapsed()
                    ->columnSpanFull()
                    ->description('Metadata à ajouter à la session Stripe pour cet événement.')
                    ->schema([
                        KeyValue::make('stripe_metadata')
                            ->label('Metadata Stripe')
                            ->keyPlaceholder('Clé de la metadata')
                            ->valuePlaceholder('Valeur de la metadata')
                            ->keyLabel('Clé')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

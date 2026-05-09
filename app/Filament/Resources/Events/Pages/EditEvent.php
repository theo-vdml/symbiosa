<?php

namespace App\Filament\Resources\Events\Pages;

use App\Enums\EventNavigationGroups;
use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Events\Schemas\EventForm;
use App\Filament\Shared\Actions\PublicationActions;
use App\Filament\Shared\Schemas\PublicationSchema;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use UnitEnum;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static ?string $navigationLabel = 'Configuration';

    protected static string|UnitEnum|null $navigationGroup = EventNavigationGroups::General;

    protected static ?string $breadcrumb = 'Configuration';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Configuration';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AdjustmentsHorizontal;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...EventForm::getBasicInfoSchema(),

                Actions::make([
                    Action::make('save')
                        ->label('Sauvegarder les modifications')
                        ->submit('save') // Cette méthode lie l'action à la soumission du formulaire
                        ->color('primary')
                        ->keyBindings(['mod+s']), // Raccourci clavier pro
                ]),

                TextEntry::make('divider')
                    ->hiddenLabel()
                    ->state(new HtmlString('<hr style="border-color: rgba(228, 228, 231, 0.1);" />'))
                    ->extraAttributes(['class' => 'py-4'])
                    ->columnSpanFull(),

                Section::make('Statut de publication')
                    ->columnSpanFull()
                    ->description('Gérez la visibilité de l\'événement sur le site.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Group::make(PublicationSchema::make()),
                                Group::make([
                                    Actions::make(PublicationActions::make("l'événement"))
                                        ->verticalAlignment('start'),
                                ]),
                            ]),
                        Callout::make("Archives")
                            ->info()
                            ->description('Pour que vos événements passés restent consultables sur votre page archives, veillez à ne pas changer leur statut en « Archivé ». Laissez-les « Publiés » et activez simplement l\'option de visibilité dédiée dans la section « Archives ».'),
                    ]),
                Section::make('Zone de danger')
                    ->collapsible()
                    ->collapsed(true)
                    ->columnSpanFull()
                    ->icon(Heroicon::OutlinedExclamationTriangle)
                    ->iconColor('danger')
                    ->schema([
                        TextEntry::make('dangerText')
                            ->hiddenLabel()
                            ->color('gray')
                            ->state("Attention, supprimer un événement est irréversible et entraînera la suppression de toutes les données associées, y compris les commandes et les tickets liés à cet événement. Assurez-vous de bien comprendre les conséquences avant de procéder."),
                        DeleteAction::make()
                            ->label(function ($record) {
                                return 'Supprimer définitivement ' . $record->title;
                            })
                            ->requiresConfirmation()
                            ->modalHeading('Confirmer la suppression')
                            ->modalDescription('Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible !')
                            ->modalSubmitActionLabel('Oui, supprimer')
                            ->color('danger'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}

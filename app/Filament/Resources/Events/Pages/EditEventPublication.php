<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Shared\Actions\PublicationActions;
use App\Filament\Shared\Schemas\PublicationSchema;
use App\Filament\Resources\Events\EventResource;
use BackedEnum;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EditEventPublication extends ViewRecord
{
    protected static string $resource = EventResource::class;

    protected static ?string $navigationLabel = 'Publication';

    protected static ?string $breadcrumb = 'Publication';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Publication';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PaperAirplane;


    protected function getHeaderActions(): array
    {
        return [];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Statut de publication')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'max-w-3xl mx-auto'])
                    ->description('Gérez la visibilité de l\'événement sur le site.')
                    ->schema([
                        ...PublicationSchema::make(),
                        Actions::make(PublicationActions::make("l'événement"))
                            ->verticalAlignment('start'),
                        Callout::make("Archives")
                            ->info()
                            ->description('Pour que vos événements passés restent consultables sur votre page archives, veillez à ne pas changer leur statut en « Archivé ». Laissez-les « Publiés » et activez simplement l\'option de visibilité dédiée dans la section « Archives ».'),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Events\Schemas\EventForm;
use Filament\Resources\Pages\Concerns\HasWizard;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Icons\Heroicon;

class CreateEvent extends CreateRecord
{
    use HasWizard;

    protected static ?string $title = 'Créer un événement';

    protected static string $resource = EventResource::class;

    public function getSteps(): array
    {
        return [
            Step::make('Informations générales')
                ->schema(EventForm::getBasicInfoSchema())
                ->icon(Heroicon::InformationCircle)
                ->description('Décrivez l\'événement.'),
            Step::make('Date et lieu')
                ->schema([
                    ...EventForm::getDateTimeSchema(false),
                    ...EventForm::getLocationSchema(false),
                ])
                ->icon(Heroicon::CalendarDays)
                ->description('Indiquez la date et le lieu.'),
            Step::make('Identité visuelle')
                ->schema(EventForm::getVisualsSchema())
                ->icon(Heroicon::Swatch)
                ->description('Personnalisez l\'identité visuelle.'),
        ];
    }
}

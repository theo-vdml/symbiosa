<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Events\Schemas\EventForm;
use BackedEnum;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EditEventDetails extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static ?string $navigationLabel = 'Détails de l\'événement';

    protected static ?string $breadcrumb = 'Détails de l\'événement';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Détails de l\'événement';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AdjustmentsHorizontal;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(EventForm::getDetailsSchema());
    }
}

<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Events\Schemas\EventForm;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static ?string $navigationLabel = 'Général';

    protected static ?string $breadcrumb = 'Général';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Général';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...EventForm::getBasicInfoSchema(),
                ...EventForm::getDateTimeSchema(),
                ...EventForm::getLocationSchema(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

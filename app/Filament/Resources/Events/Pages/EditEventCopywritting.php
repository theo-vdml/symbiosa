<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Events\Schemas\EventForm;
use BackedEnum;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EditEventCopywritting extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static ?string $navigationLabel = 'Copywritting';

    protected static ?string $breadcrumb = 'Copywritting';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Copywritting';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PencilSquare;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(EventForm::getCopywrittingSchema());
    }
}

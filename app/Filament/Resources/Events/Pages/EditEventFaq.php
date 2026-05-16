<?php

namespace App\Filament\Resources\Events\Pages;

use App\Enums\EventNavigationGroups;
use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Events\Schemas\EventForm;
use BackedEnum;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class EditEventFaq extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static string|UnitEnum|null $navigationGroup = EventNavigationGroups::Copywriting;

    protected static ?string $navigationLabel = 'Foire aux questions';

    protected static ?string $breadcrumb = 'Foire aux questions';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Foire aux questions';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(EventForm::getFaqSchema());
    }
}

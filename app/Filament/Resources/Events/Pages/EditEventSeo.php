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

class EditEventSeo extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected static ?string $navigationLabel = 'SEO';

    protected static string|UnitEnum|null $navigationGroup = EventNavigationGroups::General;

    protected static ?string $breadcrumb = 'SEO';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - SEO';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(EventForm::getSeoSchema());
    }

    /**
     * Étape 1 : On vide le cache Eloquent de la relation pour être sûr
     * de lire les nouvelles données écrites en BDD.
     */
    protected function afterSave(): void
    {
        $this->record->unsetRelation('seo');
        $this->record->load('seo');
    }

    /**
     * Étape 2 : CORRECTION DU BUG. On force Filament à recharger
     * l'état du formulaire complet à partir du record fraîchement rafraîchi.
     */
    protected function afterFill(): void
    {
        // Laisser Filament faire son premier remplissage au chargement initial
    }

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        // On profite de la fin du cycle de sauvegarde pour forcer Livewire
        // à réinjecter le nouvel état de la BDD dans les inputs du formulaire.
        $this->fillForm();

        return parent::getSavedNotification();
    }
}

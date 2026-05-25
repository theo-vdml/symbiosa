<?php

namespace App\Filament\Shared\Services;

use App\Services\SeoProcessor;
use Filament\Schemas\Components\Utilities\Get;
use Livewire\Component as Livewire;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SeoPreviewService
{
    /**
     * Centralise la récupération des données complètes via le SeoProcessor
     */
    public static function getAnalysis(Get $get, Livewire $livewire): array
    {

        $source = method_exists($livewire, 'getRecord') ? $livewire->getRecord() : null;

        $originalAttributes = [];

        if ($source && property_exists($livewire, 'data')) {
            $formData = $livewire->data ?? [];

            // 2. SAUVEGARDE DE L'ÉTAT INITIAL : On garde les vrais attributs de côté
            $originalAttributes = $source->getAttributes();

            // 3. HYDRATATION TEMPORAIRE : On injecte les données dirty du formulaire
            // directement dans le modèle original sans passer par un clone.
            foreach ($formData as $key => $value) {
                $source->setAttribute($key, $value);
            }
        }

        // Si aucun modèle n'est trouvé, on fallback sur le composant Livewire
        $source = $source ?? $livewire;

        $baseData = [];
        $fields = ['title', 'description', 'keywords', 'robots', 'canonical_url', 'og_title', 'og_description', 'og_image', 'twitter_card'];

        foreach ($fields as $field) {
            $baseData[$field] = $get("{$field}");
        }

        // 2. On extrait les configurations du trait HasSEO sur le modèle source
        $fallbacks = ($source && method_exists($source, 'getSeoFallbacks')) ? $source->getSeoFallbacks() : [];
        $defaults = ($source && method_exists($source, 'getSeoDefaults')) ? $source->getSeoDefaults() : [];

        // 3. On fait tourner le processeur principal unifié
        $analysis = SeoProcessor::analyze($baseData, $fallbacks, $defaults, $source);

        if ($source && method_exists($livewire, 'getRecord') && !empty($originalAttributes)) {
            $source->setRawAttributes($originalAttributes);
        }

        // 4. Traitement à chaud de l'image (Filament Upload State)
        $analysis['data']['og_image'] = static::resolveLivewireImageUrl($analysis['data']['og_image']);

        return $analysis;
    }

    /**
     * Transforme l'état d'un FileUpload (Object ou path) en URL valide pour la preview
     */
    protected static function resolveLivewireImageUrl(mixed $state): string
    {
        if ($state instanceof TemporaryUploadedFile) {
            try {
                return $state->temporaryUrl();
            } catch (\Exception $e) {
                return "https://placehold.co/1200x650?text=Upload+Error";
            }
        }

        if (is_array($state)) {
            return static::resolveLivewireImageUrl(reset($state));
        }

        if (is_string($state) && !empty($state)) {
            return (str_starts_with($state, 'http://') || str_starts_with($state, 'https://'))
                ? $state
                : asset('storage/' . $state);
        }

        return "https://placehold.co/1200x650?text=No+Image+Selected";
    }
}

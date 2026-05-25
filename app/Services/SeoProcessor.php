<?php

namespace App\Services;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Class SeoProcessor
 *
 * Handles the logic for merging, resolving in cascade, and transforming
 * SEO metadata from Eloquent models and global application rules.
 */
class SeoProcessor
{
    protected array $baseData = [];
    protected array $fallbacks = [];
    protected array $defaults = [];
    protected mixed $source = null;

    protected array $resolved = [];
    protected array $resolving = []; // Lock mechanism to prevent infinite loops

    public function __construct(array $baseData, array $fallbacks = [], array $defaults = [], mixed $source = null)
    {
        $this->baseData = $baseData;
        // Merge application-wide global fallbacks with model-specific fallbacks
        $this->fallbacks = array_merge($this->getGlobalFallbacks(), $fallbacks);
        $this->defaults = $defaults;
        $this->source = $source;
    }

    /**
     * Initialize and run the SEO engine.
     */
    public static function make(array $baseData, array $fallbacks = [], array $defaults = [], mixed $source = null): array
    {
        $processor = new static($baseData, $fallbacks, $defaults, $source);
        return $processor->run();
    }

    /**
     * Resolve all supported SEO fields.
     */
    public function run(): array
    {
        foreach ($this->getSeoFields() as $field) {
            $this->resolveField($field);
        }

        return $this->applyTransformations($this->resolved);
    }

    /**
     * Analyze SEO data and provide metrics and a score based on common best practices.
     */
    public static function analyze(array $baseData, array $fallbacks = [], array $defaults = [], mixed $source = null): array
    {
        $processor = new static($baseData, $fallbacks, $defaults, $source);
        $data = $processor->run();

        // 1. Title Metrics
        $titleLength = mb_strlen($data['title'] ?? '');
        $titleStatus = 'good';
        if ($titleLength === 0) $titleStatus = 'empty';
        elseif ($titleLength < 30) $titleStatus = 'warning';
        elseif ($titleLength > 60) $titleStatus = 'danger';

        // 2. Description Metrics
        $descLength = mb_strlen($data['description'] ?? '');
        $descStatus = 'good';
        if ($descLength === 0) $descStatus = 'empty';
        elseif ($descLength < 120) $descStatus = 'warning';
        elseif ($descLength > 160) $descStatus = 'danger';

        // 3. Keywords Metrics
        $keywordsCount = !empty($data['keywords']) ? count(explode(',', $data['keywords'])) : 0;
        $keywordsStatus = 'good';
        if ($keywordsCount === 0) $keywordsStatus = 'warning';
        elseif ($keywordsCount > 10) $keywordsStatus = 'danger'; // Keyword stuffing potentiel

        // 4. Robots Indexation
        $robots = $data['robots'] ?? 'index, follow';
        $robotsStatus = str_contains($robots, 'noindex') ? 'danger' : 'good';

        // 5. Canonical URL
        $hasCanonical = !empty($data['canonical_url']);
        $canonicalStatus = $hasCanonical ? 'good' : 'warning';

        // 6. Open Graph Image Presence
        $hasOgImage = !empty($data['og_image']) && !str_contains($data['og_image'], 'No+Image+Selected');
        $ogImageStatus = $hasOgImage ? 'good' : 'danger';

        // 7. Title Consistency (Est-ce que le titre OG est différent ou calqué sur le titre SEO ?)
        $ogTitle = $data['og_title'] ?? '';
        $title = $data['title'] ?? '';
        $isConsistent = !empty($ogTitle) && $ogTitle !== $title;
        $consistencyStatus = !empty($ogTitle) ? 'good' : 'empty';

        // --- CALCUL DU SCORE GLOBAL (Sur 100) ---
        $score = 100;
        if ($titleStatus === 'empty') $score -= 25;
        if ($titleStatus === 'warning' || $titleStatus === 'danger') $score -= 10;

        if ($descStatus === 'empty') $score -= 25;
        if ($descStatus === 'warning' || $descStatus === 'danger') $score -= 10;

        if ($keywordsStatus === 'warning') $score -= 1;
        if ($keywordsStatus === 'danger') $score -= 5;
        if ($robotsStatus === 'danger') $score -= 15;
        if ($canonicalStatus === 'warning') $score -= 5;
        if ($ogImageStatus === 'danger') $score -= 15;

        return [
            'data' => $data,
            'score' => max(0, $score),
            'metrics' => [
                'title' => [
                    'label'  => 'Titre SEO',
                    'value'  => "{$titleLength} / 60 car.",
                    'status' => $titleStatus,
                    'help'   => match ($titleStatus) {
                        'empty'   => 'Balise requise pour l\'indexation et le positionnement.',
                        'warning' => 'Longueur insuffisante. Intégrez vos mots-clés principaux.',
                        'danger'  => 'Longueur excessive. Le titre sera tronqué dans les résultats de recherche.',
                        default   => 'Longueur optimale.'
                    }
                ],
                'description' => [
                    'label'  => 'Meta Description',
                    'value'  => "{$descLength} / 160 car.",
                    'status' => $descStatus,
                    'help'   => match ($descStatus) {
                        'empty'   => 'Absente. Recommandée pour optimiser le taux de clic (CTR).',
                        'warning' => 'Longueur insuffisante pour décrire efficacement le contenu.',
                        'danger'  => 'Longueur excessive. Le texte sera tronqué à l\'affichage.',
                        default   => 'Longueur optimale.'
                    }
                ],
                'keywords' => [
                    'label'  => 'Mots-clés',
                    'value'  => "{$keywordsCount} / 10",
                    'status' => $keywordsStatus,
                    'help'   => match (true) {
                        $keywordsCount > 10 => 'Densité excessive. Risque de sur-optimisation (keyword stuffing).',
                        $keywordsCount === 0 => 'Optionnel. Permet de documenter la structure sémantique de la page.',
                        default              => 'Volume de mots-clés conforme aux recommandations.'
                    }
                ],
                'robots' => [
                    'label'  => 'Indexation (Robots)',
                    'value'  => $robotsStatus === 'danger' ? 'Désactivée (noindex)' : 'Activée (index)',
                    'status' => $robotsStatus,
                    'help'   => $robotsStatus === 'danger'
                        ? 'Directive restrictive détectée : la page est masquée pour les moteurs de recherche.'
                        : 'La page est accessible et indexable par les robots d\'exploration.'
                ],
                'canonical' => [
                    'label'  => 'URL Canonique',
                    'value'  => $hasCanonical ? 'Configurée' : 'Non spécifiée',
                    'status' => $canonicalStatus,
                    'help'   => $hasCanonical
                        ? 'Indique explicitement l\'URL de référence pour éviter le contenu dupliqué.'
                        : 'Absence de balise de centralisation du signal SEO (risque de duplicate content).'
                ],
                'og_image' => [
                    'label'  => 'Image Open Graph',
                    'value'  => $hasOgImage ? 'Définie' : 'Manquante',
                    'status' => $ogImageStatus,
                    'help'   => $hasOgImage
                        ? 'Assure un affichage visuel optimal lors des partages sur les plateformes sociales.'
                        : 'Aucun visuel associé. Les partages utiliseront une image par défaut ou aléatoire.'
                ],
                'og_title' => [
                    'label'  => 'Titre Open Graph',
                    'value'  => $isConsistent ? 'Personnalisé' : 'Hérité du SEO',
                    'status' => $consistencyStatus,
                    'help'   => $isConsistent
                        ? 'Titre spécifiquement optimisé pour les flux et réseaux sociaux.'
                        : 'Utilise la balise Titre SEO par défaut en l\'absence de valeur spécifique.'
                ],
            ]
        ];
    }

    /**
     * Resolve a specific SEO field and cache its result.
     * If a field depends on another via 'seo:', it halts and resolves the parent field first.
     */
    protected function resolveField(string $field): mixed
    {
        // 1. Return cached value if already resolved during this lifecycle
        if (array_key_exists($field, $this->resolved)) {
            return $this->resolved[$field];
        }

        // 2. Circular dependency protection (e.g., A needs B, B needs A)
        if (isset($this->resolving[$field])) {
            throw new \LogicException("Circular dependency detected for SEO field: {$field}");
        }

        $this->resolving[$field] = true;

        // Step A: Check for explicit manually-entered DB data
        $value = $this->baseData[$field] ?? null;

        // Step B: If empty, execute localized or global fallbacks
        if (empty($value) && !empty($this->fallbacks[$field])) {
            $value = $this->resolveFallback($this->fallbacks[$field]);
        }

        // Step C: If still empty, apply static or closure defaults
        if (empty($value)) {
            $value = $this->defaults[$field] ?? null;

            if (is_callable($value)) {
                $value = $value($this->source);
            }
        }

        $this->resolved[$field] = $value;
        unset($this->resolving[$field]);

        return $value;
    }

    /**
     * Evaluate fallback rules.
     * Differentiates between model attributes ('title') and computed SEO values ('seo:title').
     */
    protected function resolveFallback(mixed $fallbackRules): string|null
    {
        $rules = is_array($fallbackRules) ? $fallbackRules : [$fallbackRules];

        foreach ($rules as $rule) {
            // If the rule starts with 'seo:', it targets a computed SEO field
            if (str_starts_with($rule, 'seo:')) {
                $seoField = str_replace('seo:', '', $rule);

                // Recursively resolve the target SEO field immediately
                $value = $this->resolveField($seoField);
                if (!empty($value)) {
                    return (string) $value;
                }
                continue;
            }

            // If the rule starts with 'media:', it targets a media collection on the model
            if (str_starts_with($rule, 'media:')) {
                // Extract the collection name
                $collection = str_replace('media:', '', $rule);

                // First check if the source has a attribute matching the media collection name
                // (this occurs when using SEO on a Filament form that populate a clone of the model with the form state, including media uploads)
                if (data_has($this->source, $collection)) {
                    // If it's a media upload state, we need to resolve the temporary URL for preview purposes
                    $value = data_get($this->source, $collection);
                    // If it's an array (e.g., multiple uploads), take the first one for the fallback
                    if (is_array($value)) {
                        $value = reset($value);
                    }
                    // If it's a TemporaryUploadedFile, get the temporary URL
                    if ($value instanceof TemporaryUploadedFile) {
                        try {
                            return $value->temporaryUrl();
                        } catch (\Exception $e) {
                            return "https://placehold.co/1200x650?text=Upload+Error";
                        }
                    }

                    if (empty($value)) {
                        return "https://placehold.co/1200x650?text=No+Image+Selected";
                    }
                }

                $value = $this->source?->getFirstMediaUrl($collection);
                if (!empty($value)) {
                    return (string) $value;
                };
                continue;
            }

            // Otherwise, target a standard attribute or relationship on the model source
            if ($this->source) {
                $value = data_get($this->source, $rule);
                if (!empty($value)) {
                    return (string) $value;
                }
            }
        }

        return null;
    }

    /**
     * Application-wide global fallback maps.
     * Prefixed with 'seo:' to chain dependencies upon already-resolved values.
     */
    protected function getGlobalFallbacks(): array
    {
        return [
            'og_title'            => 'seo:title',
            'og_description'      => 'seo:description',
            'twitter_title'       => ['seo:og_title', 'seo:title'],
            'twitter_description' => ['seo:og_description', 'seo:description'],
            'twitter_image'       => 'seo:og_image',
        ];
    }

    /**
     * Array list of all supported SEO fields.
     * Key order does NOT matter thanks to the recursive resolution engine.
     */
    protected function getSeoFields(): array
    {
        return [
            // Base SEO fields
            'title',
            'description',
            'keywords',
            'robots',
            'canonical_url',

            // Open Graph fields
            'og_title',
            'og_description',
            'og_image',
            'og_type',

            // Twitter Card fields
            'twitter_card',
            'twitter_title',
            'twitter_description',
            'twitter_image',

            // Structured data
            'json_ld'
        ];
    }

    /**
     * Cast, clean, and format output values.
     */
    protected function applyTransformations(array $data): array
    {
        if (isset($data['keywords']) && is_array($data['keywords'])) {
            $data['keywords'] = implode(', ', $data['keywords']);
        }

        if (isset($data['json_ld']) && is_array($data['json_ld'])) {
            $data['json_ld'] = json_encode($data['json_ld']);
        }

        return $data;
    }
}

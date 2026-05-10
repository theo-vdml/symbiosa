<?php

namespace App\Filament\Shared\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\KeyValue;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SeoSchema
{
    /**
     * @param array{title?: string|callable, description?: string|callable, image?: string|callable} $fallbacks
     */
    public static function make(array $fallbacks = [], bool $withRelationship = true, ?string $prefix = null): Section
    {
        $section = Section::make('SEO & Réseaux Sociaux')
            ->columnSpanFull()
            ->icon('heroicon-o-globe-alt')
            ->description('Gérez le référencement et l\'apparence de cette page sur le web.')
            ->collapsible();

        if ($withRelationship) {
            $section->relationship('seo');
        }

        $p = $prefix ? "{$prefix}_" : "";
        $isNested = $withRelationship;

        return $section->schema([
            Tabs::make('SEO Configuration')
                ->tabs([
                    Tab::make('Recherche')
                        ->icon('heroicon-m-magnifying-glass')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    Group::make([
                                        TextInput::make("{$p}title")
                                            ->label('Titre SEO')
                                            ->placeholder(fn($get, $livewire, $component) => static::resolveFallback($get, $livewire, $component, 'title', $fallbacks, $isNested) ?: 'Titre de la page')
                                            ->maxLength(255),
                                        Textarea::make("{$p}description")
                                            ->label('Description SEO')
                                            ->placeholder(fn($get, $livewire, $component) => static::resolveFallback($get, $livewire, $component, 'description', $fallbacks, $isNested) ?: 'Description de la page...')
                                            ->rows(3),
                                        TagsInput::make("{$p}keywords")
                                            ->label('Mots-clés')
                                            ->placeholder('Ajouter un mot-clé...'),
                                        Grid::make(2)
                                            ->schema([
                                                Select::make("{$p}robots")
                                                    ->label('Robots')
                                                    ->options([
                                                        'index, follow' => 'Index, Follow',
                                                        'noindex, follow' => 'No Index, Follow',
                                                        'index, nofollow' => 'Index, No Follow',
                                                        'noindex, nofollow' => 'No Index, No Follow',
                                                    ])
                                                    ->default('index, follow'),
                                                TextInput::make("{$p}canonical_url")
                                                    ->label('URL Canonique')
                                                    ->url()
                                                    ->placeholder('https://...'),
                                            ]),
                                    ])->columnSpan(2),
                                    Group::make([
                                        Section::make('Aperçu Google')
                                            ->compact()
                                            ->headerActions([
                                                Action::make('refresh_google')
                                                    ->label('Actualiser')
                                                    ->icon('heroicon-m-arrow-path')
                                                    ->color('gray')
                                                    ->action(fn() => null),
                                            ])
                                            ->schema([
                                                Placeholder::make('google_preview')
                                                    ->hiddenLabel()
                                                    ->content(fn($get, $livewire, $component) => static::getGooglePreview($get, $livewire, $component, $fallbacks, $p, $isNested)),
                                            ]),
                                    ])->columnSpan(1),
                                ]),
                        ]),
                    Tab::make('Partage Social')
                        ->icon('heroicon-m-share')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    Group::make([
                                        TextInput::make("{$p}og_title")
                                            ->label('Titre de Partage')
                                            ->placeholder(fn($get, $livewire, $component) => static::resolveFallback($get, $livewire, $component, 'title', $fallbacks, $isNested) ?: 'Titre de partage'),
                                        Textarea::make("{$p}og_description")
                                            ->label('Description de Partage')
                                            ->placeholder(fn($get, $livewire, $component) => static::resolveFallback($get, $livewire, $component, 'description', $fallbacks, $isNested) ?: 'Description de partage...'),
                                        Grid::make(2)
                                            ->schema([
                                                Select::make("{$p}og_type")
                                                    ->label('Type de contenu')
                                                    ->options([
                                                        'website' => 'Site web',
                                                        'article' => 'Article',
                                                        'event' => 'Événement',
                                                    ])
                                                    ->default('website'),
                                                Select::make("{$p}twitter_card")
                                                    ->label('Format Twitter')
                                                    ->options([
                                                        'summary' => 'Petite image',
                                                        'summary_large_image' => 'Grande image',
                                                    ])
                                                    ->default('summary_large_image'),
                                            ]),
                                        FileUpload::make("{$p}og_image")
                                            ->label('Image de Partage')
                                            ->image()
                                            ->directory('seo/og')
                                            ->disk('public')
                                            ->imageEditor(),
                                    ])->columnSpan(2),
                                    Group::make([
                                        Section::make('Aperçu Twitter')
                                            ->compact()
                                            ->headerActions([
                                                Action::make('refresh_twitter')
                                                    ->label('Actualiser')
                                                    ->icon('heroicon-m-arrow-path')
                                                    ->color('gray')
                                                    ->action(fn() => null),
                                            ])
                                            ->schema([
                                                Placeholder::make('twitter_preview')
                                                    ->hiddenLabel()
                                                    ->content(fn($get, $livewire, $component) => static::getTwitterPreview($get, $livewire, $component, $fallbacks, $p, $isNested)),
                                            ]),
                                    ])->columnSpan(1),
                                ]),
                        ]),
                    Tab::make('Avancé')
                        ->icon('heroicon-m-code-bracket')
                        ->schema([
                            KeyValue::make("{$p}json_ld")
                                ->label('Données Structurées (JSON-LD)')
                                ->keyLabel('Propriété')
                                ->valueLabel('Valeur')
                                ->addActionLabel('Ajouter une propriété'),
                        ]),
                ]),
        ]);
    }

    protected static function resolveFallback($get, $livewire, $component, string $type, array $fallbacks, bool $isNested): ?string
    {
        $fallback = $fallbacks[$type] ?? null;
        if (!$fallback) return null;

        $parentRecord = method_exists($livewire, 'getRecord') ? $livewire->getRecord() : null;

        // 1. If it's a closure, run it
        if (is_callable($fallback)) {
            return $fallback($parentRecord, $get);
        }

        // 2. If it's a string, treat as field name
        if (is_string($fallback)) {
            // Priority 1: Direct Livewire data access (works even without .live() if refresh triggered)
            // Filament stores state in $livewire->data
            $stateValue = $livewire->data[$fallback] ?? null;
            if ($stateValue && is_string($stateValue) && !empty(strip_tags($stateValue))) {
                return strip_tags($stateValue);
            }

            // Priority 2: Database Record (for dedicated pages)
            if ($parentRecord) {
                $dbValue = $parentRecord->{$fallback} ?? null;
                if (is_string($dbValue) && !empty(strip_tags($dbValue))) {
                    return strip_tags($dbValue);
                }
            }
        }

        return null;
    }

    protected static function getImageUrl($get, $livewire, $component, array $fallbacks, string $p, bool $isNested): string
    {
        $imageState = $get("{$p}og_image");

        // Handle current field upload
        if ($imageState instanceof TemporaryUploadedFile) {
            try { return $imageState->temporaryUrl(); } catch (\Exception $e) {}
        }
        if (is_array($imageState)) {
            $firstFile = reset($imageState);
            if ($firstFile instanceof TemporaryUploadedFile) {
                try { return $firstFile->temporaryUrl(); } catch (\Exception $e) {}
            }
            if (is_string($firstFile)) { return asset("storage/{$firstFile}"); }
        }
        if (is_string($imageState) && !empty($imageState)) {
             return asset("storage/{$imageState}");
        }

        // Handle Fallback
        $fallback = $fallbacks['image'] ?? null;
        if ($fallback && is_string($fallback)) {
            // Try Livewire data first (handles temporary uploads in parent form)
            $formImage = $livewire->data[$fallback] ?? null;
            
            if ($formImage instanceof TemporaryUploadedFile) {
                try { return $formImage->temporaryUrl(); } catch (\Exception $e) {}
            }
            if (is_array($formImage)) {
                $firstFile = reset($formImage);
                if ($firstFile instanceof TemporaryUploadedFile) {
                    try { return $firstFile->temporaryUrl(); } catch (\Exception $e) {}
                }
                if (is_string($firstFile)) { return asset("storage/{$firstFile}"); }
            }
            if (is_string($formImage) && !empty($formImage)) {
                return asset("storage/{$formImage}");
            }

            // Try DB record
            $parentRecord = method_exists($livewire, 'getRecord') ? $livewire->getRecord() : null;
            if ($parentRecord) {
                $dbImage = $parentRecord->{$fallback} ?? null;
                if (is_string($dbImage) && !empty($dbImage)) {
                    return asset("storage/{$dbImage}");
                }
            }
        }

        return "https://via.placeholder.com/1200x630.png?text=Aper%C3%A7u+Image";
    }

    protected static function getGooglePreview($get, $livewire, $component, array $fallbacks, string $p, bool $isNested): HtmlString
    {
        $title = $get("{$p}title") ?: static::resolveFallback($get, $livewire, $component, 'title', $fallbacks, $isNested);
        $title = $title ?: 'Titre de la page';

        $description = $get("{$p}description") ?: static::resolveFallback($get, $livewire, $component, 'description', $fallbacks, $isNested);
        $description = $description ?: 'Description de la page...';

        return new HtmlString("
            <div class='bg-white p-4 border border-gray-200 rounded-lg shadow-sm text-left'>
                <div class='flex items-center gap-2 mb-1'>
                    <div class='bg-gray-100 rounded-full w-7 h-7 flex items-center justify-center text-xs text-gray-500 font-bold'>G</div>
                    <div class='flex flex-col'>
                        <div class='text-[14px] text-[#202124] leading-tight font-sans'>Symbiosa</div>
                        <div class='text-[12px] text-[#5f6368] flex items-center gap-1 font-sans'>https://symbiosa.be <span class='text-[8px]'>▼</span></div>
                    </div>
                </div>
                <div class='text-[#1a0dab] text-[20px] font-medium hover:underline cursor-pointer mb-1 font-sans'>{$title}</div>
                <div class='text-[#4d5156] text-[14px] leading-snug line-clamp-2 font-sans'>{$description}</div>
            </div>
        ");
    }

    protected static function getTwitterPreview($get, $livewire, $component, array $fallbacks, string $p, bool $isNested): HtmlString
    {
        $title = $get("{$p}og_title") ?: $get("{$p}title");
        if (empty($title)) {
            $title = static::resolveFallback($get, $livewire, $component, 'title', $fallbacks, $isNested);
        }
        $title = $title ?: 'Titre de la page';

        $description = $get("{$p}og_description") ?: $get("{$p}description");
        if (empty($description)) {
            $description = static::resolveFallback($get, $livewire, $component, 'description', $fallbacks, $isNested);
        }
        $description = $description ?: 'Découvrez notre univers sur Symbiosa.be ! #techno #event';

        $card = $get("{$p}twitter_card") ?? 'summary_large_image';
        $imageUrl = static::getImageUrl($get, $livewire, $component, $fallbacks, $p, $isNested);

        $now = now()->format('H:i · j M Y');

        return new HtmlString("
            <div class='bg-white border border-gray-100 rounded-xl p-4 max-w-[500px] shadow-sm font-sans text-left'>
                <div class='flex items-start gap-3 mb-3 text-left'>
                    <div class='w-12 h-12 rounded-full bg-[#06402B] overflow-hidden flex-shrink-0 flex items-center justify-center'>
                        <span class='text-white font-bold text-lg font-sans'>S</span>
                    </div>
                    <div class='flex flex-col text-left'>
                        <div class='flex items-center gap-1'>
                            <span class='font-bold text-black text-[15px] font-sans'>Symbiosa</span>
                            <span class='text-gray-500 text-[15px] font-sans'>@symbiosa_be</span>
                        </div>
                        <div class='text-black text-[15px] mt-0.5 leading-tight font-sans'>Just sharing this</div>
                    </div>
                </div>

                <div class='border border-gray-200 rounded-2xl overflow-hidden mb-3 hover:bg-gray-50 cursor-pointer transition-colors'>
                    " . ($card === 'summary_large_image' ? "
                        <div class='aspect-[1.91/1] w-full bg-gray-100 relative'>
                            <img src='{$imageUrl}' class='w-full h-full object-cover' />
                        </div>
                        <div class='p-3 border-t border-gray-100'>
                            <div class='text-gray-500 text-[13px] mb-0.5 font-sans'>symbiosa.be</div>
                            <div class='text-black font-bold text-[14px] truncate font-sans'>{$title}</div>
                            <div class='text-gray-600 text-[14px] line-clamp-2 mt-0.5 leading-tight font-sans'>{$description}</div>
                        </div>
                    " : "
                        <div class='flex h-[120px]'>
                            <div class='w-[120px] h-[120px] flex-shrink-0 bg-gray-100'>
                                <img src='{$imageUrl}' class='w-full h-full object-cover' />
                            </div>
                            <div class='p-3 flex flex-col justify-center min-w-0 border-l border-gray-100 w-full text-left'>
                                <div class='text-gray-500 text-[13px] mb-0.5 font-sans'>symbiosa.be</div>
                                <div class='text-black font-bold text-[14px] truncate font-sans'>{$title}</div>
                                <div class='text-gray-600 text-[14px] line-clamp-2 mt-0.5 leading-tight font-sans'>{$description}</div>
                            </div>
                        </div>
                    ") . "
                </div>

                <div class='text-gray-500 text-[14px] border-b border-gray-100 pb-3 mb-3 font-sans text-left'>
                    {$now} · <span class='text-[#1d9bf0] hover:underline cursor-pointer'>Twitter for Web</span>
                </div>
                <div class='flex items-center gap-5 text-gray-500 text-[14px] font-sans'>
                    <span><span class='text-black font-bold'>10</span> Retweets</span>
                    <span><span class='text-black font-bold'>125</span> Likes</span>
                </div>
            </div>
        ");
    }
}

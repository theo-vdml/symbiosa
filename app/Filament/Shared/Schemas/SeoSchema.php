<?php

namespace App\Filament\Shared\Schemas;

use App\Filament\Shared\Services\SeoPreviewService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;

class SeoSchema
{
    public static function make(): Section
    {
        $section = Section::make('SEO & Réseaux Sociaux')
            ->contained(false)
            ->icon('heroicon-o-globe-alt')
            ->columnSpanFull()
            ->relationship('seo');

        return $section->schema([
            Tabs::make('SEO Configuration')
                ->tabs([
                    static::getSearchTab(),
                    static::getSocialTab(),
                    static::getAdvancedTab(),
                    Tab::make('Score')
                        ->icon(Heroicon::ChartPie)
                        ->schema([
                            ViewField::make('seo_score_widget')
                                ->hiddenLabel()
                                ->view('filament.forms.components.seo.score-widget', fn($get, $livewire) => [
                                    'analysis' => SeoPreviewService::getAnalysis($get, $livewire)
                                ]),
                        ]),
                ]),
        ]);
    }

    protected static function getSearchTab(): Tab
    {
        return Tabs\Tab::make('Recherche')
            ->icon('heroicon-m-magnifying-glass')
            ->schema([
                Grid::make(5)->schema([
                    Group::make([
                        TextInput::make("title")
                            ->label('Titre SEO')
                            ->placeholder(fn($get, $livewire) => self::getSeoPlaceholder($get, $livewire, 'title', 'Donnez un titre à votre page'))
                            ->extraInputAttributes(
                                fn($get, $livewire) =>
                                self::isSeoInferred($get, $livewire, 'title')
                                    ? ['class' => 'placeholder:text-purple-600 dark:placeholder:text-purple-300']
                                    : []
                            )
                            ->hint(fn($get, $livewire) => self::isSeoInferred($get, $livewire, 'title') ? 'Automatique' : null)
                            ->hintIcon(fn($get, $livewire) => self::isSeoInferred($get, $livewire, 'title') ? Heroicon::Sparkles : null)
                            ->hintColor('purple')
                            ->live(onBlur: true),
                        Textarea::make("description")
                            ->placeholder(fn($get, $livewire) => self::getSeoPlaceholder($get, $livewire, 'description', 'Donnez une description à votre page'))
                            ->extraInputAttributes(
                                fn($get, $livewire) =>
                                self::isSeoInferred($get, $livewire, 'description')
                                    ? ['class' => 'placeholder:text-purple-600 dark:placeholder:text-purple-300']
                                    : []
                            )
                            ->hint(fn($get, $livewire) => self::isSeoInferred($get, $livewire, 'description') ? 'Automatique' : null)
                            ->hintIcon(fn($get, $livewire) => self::isSeoInferred($get, $livewire, 'description') ? Heroicon::Sparkles : null)
                            ->hintColor('purple')
                            ->rows(3)
                            ->autosize()
                            ->live(onBlur: true),
                        TagsInput::make("keywords")
                            ->label('Mots-clés'),
                        Grid::make(2)->schema([
                            Select::make("robots")
                                ->options(['index, follow' => 'Index, Follow', 'noindex, follow' => 'No Index, Follow'])
                                ->default('index, follow'),
                            TextInput::make("canonical_url")
                                ->label('URL Canonique')
                                ->placeholder(fn($get, $livewire) => self::getSeoPlaceholder($get, $livewire, 'canonical_url', 'Donnez une URL canonique à votre page'))
                                ->extraInputAttributes(
                                    fn($get, $livewire) =>
                                    self::isSeoInferred($get, $livewire, 'canonical_url')
                                        ? ['class' => 'placeholder:text-purple-600 dark:placeholder:text-purple-300']
                                        : []
                                )
                                ->hint(fn($get, $livewire) => self::isSeoInferred($get, $livewire, 'canonical_url') ? 'Automatique' : null)
                                ->hintIcon(fn($get, $livewire) => self::isSeoInferred($get, $livewire, 'canonical_url') ? Heroicon::Sparkles : null)
                                ->hintColor('purple')
                                ->url(),
                        ]),
                    ])->columnSpan(3),

                    Section::make('Aperçu Google')
                        ->columnSpan(2)
                        ->contained(false)
                        ->headerActions([
                            Action::make('refresh')->hiddenLabel()->link()->icon(Heroicon::ArrowPath)->action(fn() => null)
                        ])
                        ->schema([
                            ViewField::make('google_preview')
                                ->hiddenLabel()
                                ->view('filament.forms.components.seo.google-preview', function ($get, $livewire) {
                                    $analysis = SeoPreviewService::getAnalysis($get, $livewire);
                                    return [
                                        'title' => $analysis['data']['title'],
                                        'description' => $analysis['data']['description'],
                                        'metrics' => $analysis['metrics'],
                                    ];
                                }),
                        ]),
                ]),
            ]);
    }

    protected static function getSocialTab(): Tabs\Tab
    {
        return Tabs\Tab::make('Partage Social')
            ->icon('heroicon-m-share')
            ->schema([
                Grid::make(5)->schema([
                    Group::make([
                        FileUpload::make("og_image")
                            ->label('Image de Partage')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('seo/og')
                            ->optimize('webp', 75)
                            ->live(),
                        TextInput::make("og_title")
                            ->label('Titre de Partage')
                            ->placeholder(fn($get, $livewire) => self::getSeoPlaceholder($get, $livewire, 'og_title', 'Donnez un titre pour les réseaux sociaux'))
                            ->extraInputAttributes(
                                fn($get, $livewire) =>
                                self::isSeoInferred($get, $livewire, 'og_title')
                                    ? ['class' => 'placeholder:text-purple-600 dark:placeholder:text-purple-300']
                                    : []
                            )
                            ->hint(fn($get, $livewire) => self::isSeoInferred($get, $livewire, 'og_title') ? 'Automatique' : null)
                            ->hintIcon(fn($get, $livewire) => self::isSeoInferred($get, $livewire, 'og_title') ? Heroicon::Sparkles : null)
                            ->hintColor('purple')
                            ->live(onBlur: true),
                        Textarea::make("og_description")
                            ->label('Description de Partage')
                            ->placeholder(fn($get, $livewire) => self::getSeoPlaceholder($get, $livewire, 'og_description', 'Donnez une description pour les réseaux sociaux'))
                            ->extraInputAttributes(
                                fn($get, $livewire) =>
                                self::isSeoInferred($get, $livewire, 'og_description')
                                    ? ['class' => 'placeholder:text-purple-600 dark:placeholder:text-purple-300']
                                    : []
                            )
                            ->hint(fn($get, $livewire) => self::isSeoInferred($get, $livewire, 'og_description') ? 'Automatique' : null)
                            ->hintIcon(fn($get, $livewire) => self::isSeoInferred($get, $livewire, 'og_description') ? Heroicon::Sparkles : null)
                            ->hintColor('purple')
                            ->rows(3)
                            ->autosize()
                            ->live(onBlur: true),
                        Select::make("twitter_card")
                            ->options(['summary' => 'Petite image', 'summary_large_image' => 'Grande image'])
                            ->default('summary_large_image')
                            ->live(),
                    ])->columnSpan(3),

                    Section::make('Aperçu Twitter / X')
                        ->columnSpan(2)
                        ->contained(false)
                        ->headerActions([
                            Action::make('refresh')->hiddenLabel()->link()->icon(Heroicon::ArrowPath)->action(fn() => null)
                        ])
                        ->schema([
                            ViewField::make('twitter_preview')
                                ->hiddenLabel()
                                ->view('filament.forms.components.seo.twitter-preview', function ($get, $livewire) {
                                    $analysis = SeoPreviewService::getAnalysis($get, $livewire);
                                    return [
                                        'title' => $analysis['data']['twitter_title'],
                                        'description' => $analysis['data']['twitter_description'],
                                        'image' => $analysis['data']['og_image'],
                                        'card' => $analysis['data']['twitter_card'],
                                    ];
                                }),
                        ]),
                ]),
            ]);
    }

    protected static function getAdvancedTab(): Tabs\Tab
    {
        return Tabs\Tab::make('Avancé')
            ->icon('heroicon-m-code-bracket')
            ->schema([
                KeyValue::make("json_ld")->label('Données Structurées (JSON-LD)'),
            ]);
    }

    private static function getSeoPlaceholder(Get $get, mixed $livewire, string $field, string $default = ''): string
    {
        $databaseValue = $get("{$field}");
        $inferredValue = SeoPreviewService::getAnalysis($get, $livewire)['data'][$field] ?? null;

        return (empty($databaseValue) && !empty($inferredValue)) ? $inferredValue : $default;
    }

    private static function isSeoInferred(Get $get, mixed $livewire, string $field): bool
    {
        $databaseValue = $get("{$field}");
        $inferredValue = SeoPreviewService::getAnalysis($get, $livewire)['data'][$field] ?? null;

        return empty($databaseValue) && !empty($inferredValue);
    }
}

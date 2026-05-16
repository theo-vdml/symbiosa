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
use Filament\Support\Icons\Heroicon;

class SeoSchema
{
    public static function make(bool $withRelationship = true, string $prefix = ''): Section
    {
        $p = $prefix ? "{$prefix}_" : "";

        $section = Section::make('SEO & Réseaux Sociaux')
            ->contained(false)
            ->icon('heroicon-o-globe-alt')
            ->columnSpanFull();

        if ($withRelationship) {
            $section->relationship('seo');
        }

        return  $section->schema([
            Tabs::make('SEO Configuration')
                ->tabs([
                    static::getSearchTab($p),
                    static::getSocialTab($p),
                    static::getAdvancedTab($p),
                ]),
        ]);
    }

    protected static function getSearchTab(string $p): Tabs\Tab
    {
        return Tabs\Tab::make('Recherche')
            ->icon('heroicon-m-magnifying-glass')
            ->schema([
                Grid::make(5)->schema([
                    Group::make([
                        TextInput::make("{$p}title")
                            ->placeholder("Donnez un titre a votre page")
                            ->label('Titre SEO'),
                        Textarea::make("{$p}description")
                            ->label('Description SEO')
                            ->placeholder("Donnez une description a votre page")
                            ->rows(3)
                            ->autosize(),
                        TagsInput::make("{$p}keywords")
                            ->label('Mots-clés'),
                        Grid::make(2)
                            ->schema([
                                Select::make("{$p}robots")
                                    ->options(['index, follow' => 'Index, Follow', 'noindex, follow' => 'No Index, Follow'])
                                    ->default('index, follow'),
                                TextInput::make("{$p}canonical_url")
                                    ->label('URL Canonique')
                                    ->url(),
                            ]),
                    ])->columnSpan(3),
                    Section::make('Aperçu Google')
                        ->columnSpan(2)
                        ->contained(false)
                        ->headerActions([
                            Action::make('refresh')
                                ->hiddenLabel()
                                ->link()
                                ->icon(Heroicon::ArrowPath)
                                ->action(fn() => null) // No real action, we just want to trigger a re-render
                        ])
                        ->schema([
                            ViewField::make('google_preview')
                                ->hiddenLabel()
                                ->view('filament.forms.components.seo.google-preview', fn($get, $livewire) => [
                                    'title' => SeoPreviewService::getComputedValue($get, $livewire, 'title', $p),
                                    'description' => SeoPreviewService::getComputedValue($get, $livewire, 'description', $p),
                                ]),
                        ]),
                ]),
            ]);
    }

    protected static function getSocialTab(string $p): Tabs\Tab
    {
        return Tabs\Tab::make('Partage Social')
            ->icon('heroicon-m-share')
            ->schema([
                Grid::make(5)->schema([
                    Group::make([
                        TextInput::make("{$p}og_title")
                            ->label('Titre de Partage')
                            ->placeholder('Donnez un titre pour les réseaux sociaux'),
                        Textarea::make("{$p}og_description")
                            ->label('Description de Partage')
                            ->placeholder('Donnez une description pour les réseaux sociaux'),
                        FileUpload::make("{$p}og_image")
                            ->label('Image de Partage')
                            ->image()
                            ->directory('seo/og'),
                        Select::make("{$p}twitter_card")
                            ->options(['summary' => 'Petite image', 'summary_large_image' => 'Grande image'])
                            ->default('summary_large_image'),
                    ])->columnSpan(3),
                    Section::make('Aperçu Twitter')
                        ->columnSpan(2)
                        ->contained(false)
                        ->headerActions([
                            Action::make('refresh')
                                ->hiddenLabel()
                                ->link()
                                ->icon(Heroicon::ArrowPath)
                                ->action(fn() => null)
                        ])
                        ->schema([
                            ViewField::make('twitter_preview')
                                ->hiddenLabel()
                                ->view('filament.forms.components.seo.twitter-preview', fn($get, $livewire) => [
                                    'title' => SeoPreviewService::getComputedValue($get, $livewire, 'og_title', $p),
                                    'description' => SeoPreviewService::getComputedValue($get, $livewire, 'og_description', $p),
                                    'image' => SeoPreviewService::getImageUrl($get, $livewire, $p),
                                    'card' => SeoPreviewService::getComputedValue($get, $livewire, 'twitter_card', $p),
                                ]),
                        ]),
                ]),
            ]);
    }

    protected static function getAdvancedTab(string $p): Tabs\Tab
    {
        return Tabs\Tab::make('Avancé')
            ->icon('heroicon-m-code-bracket')
            ->schema([
                KeyValue::make("{$p}json_ld")->label('Données Structurées (JSON-LD)'),
            ]);
    }
}

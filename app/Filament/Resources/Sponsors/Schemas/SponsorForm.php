<?php

namespace App\Filament\Resources\Sponsors\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SponsorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom du sponsor')
                    ->placeholder('Ex : CocaCola')
                    ->prefixIcon(Heroicon::Briefcase)
                    ->required(),
                SpatieMediaLibraryFileUpload::make('logo')
                    ->label('Logo du sponsor')
                    ->collection('logo')
                    ->disk('r2')
                    ->visibility('public')
                    ->image()
                    ->automaticallyResizeImagesMode('cover')
                    ->automaticallyResizeImagesToWidth('800')
                    ->automaticallyResizeImagesToHeight('800')
                    ->required(),
                TextInput::make('website')
                    ->label('Site web')
                    ->placeholder('Ex : https://www.coca-cola.com')
                    ->prefixIcon(Heroicon::GlobeAlt)
                    ->url(),
                Textarea::make('description')
                    ->label('Description')
                    ->placeholder('Une brève description du sponsor (optionnel)')
                    ->autosize()
                    ->rows(1)
                    ->columnSpanFull(),
            ]);
    }
}

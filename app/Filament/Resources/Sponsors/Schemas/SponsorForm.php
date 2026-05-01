<?php

namespace App\Filament\Resources\Sponsors\Schemas;

use Filament\Forms\Components\FileUpload;
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
                FileUpload::make('logo')
                    ->label('Logo du sponsor')
                    ->image()
                    ->disk('public')
                    ->directory('sponsors/logos')
                    ->visibility('public')
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

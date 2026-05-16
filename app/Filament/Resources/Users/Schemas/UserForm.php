<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom complet')
                    ->placeholder('John Doe')
                    ->prefixIcon(Heroicon::User)
                    ->required(),
                TextInput::make('email')
                    ->label('Adresse e-mail')
                    ->placeholder('john.doe@example.com')
                    ->prefixIcon(Heroicon::AtSymbol)
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('Mot de passe')
                    ->prefixIcon(Heroicon::LockClosed)
                    ->password()
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context): bool => $context === 'create'),
            ]);
    }
}

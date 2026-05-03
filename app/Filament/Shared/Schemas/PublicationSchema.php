<?php

namespace App\Filament\Shared\Schemas;

use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

class PublicationSchema
{
    public static function make(): array
    {
        return [
            TextEntry::make('status_label')
                ->label('Statut actuel')
                ->dehydrated(true)
                ->badge()
                ->size('lg')
                ->color(fn($record) => $record->status->getDynamicColor($record->published_at))
                ->icon(fn($record) => $record->status->getDynamicIcon($record->published_at))
                ->state(fn($record) => $record->status->getDynamicLabel($record->published_at)),

            TextEntry::make('published_at_view')
                ->label('Date de publication')
                ->dehydrated(true)
                ->state(function ($record) {
                    if (!$record?->published_at) {
                        return new HtmlString('<span class="text-gray-500 italic">Non défini</span>');
                    }

                    return $record->published_at->translatedFormat('l j F Y à H:i');
                }),
        ];
    }
}

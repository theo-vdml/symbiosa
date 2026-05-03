<?php

namespace App\Filament\Shared\Schemas;

use App\Enums\PublicationStatus;
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
                ->color(fn($record) => match (true) {
                    $record?->status === PublicationStatus::Archived => 'danger',
                    $record?->status === PublicationStatus::Draft => 'gray',
                    $record?->status === PublicationStatus::Published && $record->published_at?->isFuture() => 'info',
                    $record?->status === PublicationStatus::Published => 'success',
                    default => 'gray',
                })
                ->state(fn($record) => match (true) {
                    $record?->status === PublicationStatus::Archived => $record->status->getLabel(),
                    $record?->status === PublicationStatus::Draft => $record->status->getLabel(),
                    $record?->status === PublicationStatus::Published && $record->published_at?->isFuture() => 'Programmé',
                    $record?->status === PublicationStatus::Published => 'Publié',
                    default => 'Brouillon',
                }),

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

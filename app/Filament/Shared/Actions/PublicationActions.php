<?php

namespace App\Filament\Shared\Actions;

use App\Enums\PublicationStatus;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use CodeWithDennis\FilamentAdvancedChoice\Filament\Forms\Components\RadioList;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Support\Icons\Heroicon;

class PublicationActions
{
    public static function make(?string $resourceLabel = null): array
    {
        $suffix = $resourceLabel ? " $resourceLabel" : "";

        return [
            static::getPublishAction($suffix),
            static::getPublishNowAction($suffix),
            static::getArchiveAction($suffix),
            static::getRevertToDraftAction($suffix),
        ];
    }

    public static function getPublishAction(string $suffix = ''): Action
    {
        $getLabel = fn($record) => ($record->published_at ? "Republier" : "Publier") . $suffix;

        return Action::make('publish')
            ->extraAttributes(['class' => 'w-full'])
            ->label(fn($record) => $getLabel($record))
            ->icon(fn($record) => $record->published_at ? Heroicon::ArrowPath : Heroicon::PaperAirplane)
            ->color('success')
            ->visible(fn($record) => in_array($record->status, [PublicationStatus::Draft, PublicationStatus::Archived]))
            ->schema(fn($record) => [
                static::getPublishOptions($record),
                Group::make(static::getScheduleSchema())
                    ->visible(fn($get) => $get('publish_type') === 'schedule'),
            ])
            ->modalSubmitActionLabel(fn($record) => $getLabel($record))
            ->action(function ($record, array $data) {
                $record->update([
                    'status' => PublicationStatus::Published,
                    'published_at' => static::resolvePublishedAt($data, $record),
                ]);
            })
            ->after(fn($livewire) => $livewire->dispatch('$refresh'));
    }

    public static function getPublishNowAction(string $suffix = ''): Action
    {
        return Action::make('publish_now')
            ->extraAttributes(['class' => 'w-full'])
            ->label('Publier maintenant')
            ->color('success')
            ->icon('heroicon-o-paper-airplane')
            ->visible(fn($record) => $record->status === PublicationStatus::Published && $record->published_at?->isFuture())
            ->requiresConfirmation()
            ->modalHeading("Publier immédiatement ?")
            ->modalDescription("La planification sera annulée et la publication sera immédiate.")
            ->action(function ($record) {
                $record->update([
                    'status' => PublicationStatus::Published,
                    'published_at' => now(),
                ]);
            })
            ->after(fn($livewire) => $livewire->dispatch('$refresh'));
    }

    public static function getArchiveAction(string $suffix = ''): Action
    {
        return Action::make('archive')
            ->extraAttributes(['class' => 'w-full'])
            ->label('Archiver')
            ->color('warning')
            ->icon('heroicon-o-archive-box')
            ->visible(fn($record) => $record->status === PublicationStatus::Published)
            ->requiresConfirmation()
            ->modalHeading("Archiver" . $suffix . " ?")
            ->modalDescription("Ne sera plus visible sur le site mais sera conservé en archive.")
            ->action(function ($record) {
                $record->update(['status' => PublicationStatus::Archived]);
            })
            ->after(fn($livewire) => $livewire->dispatch('$refresh'));
    }

    public static function getRevertToDraftAction(string $suffix = ''): Action
    {
        return Action::make('revert_to_draft')
            ->extraAttributes(['class' => 'w-full'])
            ->label('Brouillon')
            ->color('gray')
            ->icon('heroicon-o-pencil')
            ->visible(fn($record) => $record->status !== PublicationStatus::Draft)
            ->requiresConfirmation()
            ->modalHeading('Remettre en brouillon ?')
            ->modalDescription("Sera retiré du site et sa date de publication sera effacée.")
            ->action(function ($record) {
                $record->update([
                    'status' => PublicationStatus::Draft,
                    'published_at' => null,
                ]);
            })
            ->after(fn($livewire) => $livewire->dispatch('$refresh'));
    }

    public static function getPublishOptions(mixed $record): RadioList
    {
        $publishedAt = $record->published_at ?? null;

        $options = [
            'now' => 'Immédiatement',
            'schedule' => 'Programmer plus tard',
        ];
        $descriptions = [
            'now' => "Sera publié immédiatement.",
            'schedule' => 'Choisissez une date et une heure de publication.',
        ];
        $extras = [];

        if ($publishedAt) {
            $descriptions['keep'] = "Sera publié avec sa date de publication originale.";
            $options = array_merge(['keep' => 'Conserver la date originale'], $options);
            $extras['keep'] = $publishedAt->translatedFormat('l j F Y à H:i');
        }

        return RadioList::make('publish_type')
            ->label('Date de publication')
            ->options($options)
            ->descriptions($descriptions)
            ->extras($extras)
            ->default($publishedAt ? 'keep' : 'now')
            ->live();
    }

    public static function getScheduleSchema(): array
    {
        return [
            DatePicker::make('date_only')
                ->label('Date de publication')
                ->required()
                ->native(false)
                ->displayFormat('l j F Y')
                ->default(now()->addDay())
                ->live(),

            TimePicker::make('time_only')
                ->label('Heure de publication')
                ->required()
                ->seconds(false)
                ->native(false)
                ->displayFormat('H:i')
                ->visible(fn($get) => !$get('at_nine_am'))
                ->default('09:00'),

            Toggle::make('at_nine_am')
                ->label('Publier à 09:00')
                ->default(true)
                ->live(),
        ];
    }

    protected static function resolvePublishedAt(array $data, mixed $record): CarbonInterface|Carbon|null
    {
        if ($data['publish_type'] === 'keep') {
            return $record->published_at ?? now();
        }

        if ($data['publish_type'] === 'schedule') {
            $date = Carbon::parse($data['date_only']);
            if ($data['at_nine_am']) {
                return $date->startOfDay()->addHours(9);
            }
            $time = Carbon::parse($data['time_only']);
            return $date->setTime($time->hour, $time->minute);
        }

        return now();
    }
}

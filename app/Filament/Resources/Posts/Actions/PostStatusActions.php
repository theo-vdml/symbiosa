<?php

namespace App\Filament\Resources\Posts\Actions;

use App\Enums\PostStatus;
use App\Models\Post;
use Carbon\Carbon;
use CodeWithDennis\FilamentAdvancedChoice\Filament\Forms\Components\RadioList;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Support\Icons\Heroicon;

use function Illuminate\Support\seconds;

class PostStatusActions
{
    public static function make(): array
    {
        return [
            Action::make('publish')
                ->extraAttributes(['class' => 'w-full'])
                ->label('Publier l\'article')
                ->color('success')
                ->icon('heroicon-o-paper-airplane')
                ->visible(fn(Post $record) => $record->status === PostStatus::Draft)
                ->form([
                    RadioList::make('publish_type')
                        ->columns(2)
                        ->label('Moment de publication')
                        ->options([
                            'now' => 'Immédiatement',
                            'schedule' => 'Programmer plus tard',
                        ])
                        ->descriptions([
                            'now' => 'L\'article sera publié immédiatement.',
                            'schedule' => 'Choisissez une date et une heure de publication.',
                        ])
                        ->default('now')
                        ->live(),

                    Group::make([
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
                            ->visible(fn($get) => !$get('at_nine_am')) // Visible seulement si case décochée
                            ->default('09:00'),

                        Toggle::make('at_nine_am')
                            ->label('Publier à 09:00')
                            ->default(true)
                            ->live(),

                    ])
                        ->visible(fn($get) => $get('publish_type') === 'schedule'),
                ])
                ->modalSubmitActionLabel('Publier')
                ->action(function (Post $record, array $data) {
                    $publishedAt = now();

                    if ($data['publish_type'] === 'schedule') {
                        // On récupère la date
                        $date = Carbon::parse($data['date_only']);

                        // On définit l'heure
                        if ($data['at_nine_am']) {
                            $date->hour(9)->minute(0)->second(0);
                        } else {
                            $time = Carbon::parse($data['time_only']);
                            $date->hour($time->hour)->minute($time->minute)->second(0);
                        }

                        $publishedAt = $date;
                    }

                    $record->update([
                        'status' => PostStatus::Published,
                        'published_at' => $publishedAt,
                    ]);
                })
                ->after(fn($livewire) => method_exists($livewire, 'refreshFormData') ? $livewire->refreshFormData(['status', 'published_at']) : null),

            Action::make('publish_now')
                ->extraAttributes(['class' => 'w-full']) // <--- Force la largeur 100%
                ->label('Publier maintenant')
                ->color('success')
                ->icon('heroicon-o-paper-airplane')
                ->visible(fn(Post $record) => $record->status === PostStatus::Published && $record->published_at->isFuture())
                ->requiresConfirmation()
                ->modalHeading('Publier cet article ?')
                ->modalDescription('L\'article sera publié immédiatement et la planification sera annulée.')
                ->action(function (Post $record) {
                    $record->update([
                        'status' => PostStatus::Published,
                        'published_at' => now(),
                    ]);
                })
                ->after(fn($livewire) => method_exists($livewire, 'refreshFormData') ? $livewire->refreshFormData(['status', 'published_at']) : null),

            Action::make('archive')
                ->extraAttributes(['class' => 'w-full']) // <--- Force la largeur 100%
                ->label('Archiver')
                ->color('warning')
                ->icon('heroicon-o-archive-box')
                ->visible(fn(Post $record) => $record->status === PostStatus::Published)
                ->requiresConfirmation()
                ->modalHeading('Archiver cet article ?')
                ->modalDescription('L\'article ne sera plus visible sur le site mais sera conservé en archive.')
                ->action(function (Post $record) {
                    $record->update(['status' => PostStatus::Archived]);
                })
                ->after(fn($livewire) => method_exists($livewire, 'refreshFormData') ? $livewire->refreshFormData(['status']) : null),

            Action::make('republish')
                ->extraAttributes(['class' => 'w-full']) // <--- Force la largeur 100%
                ->label('Republier')
                ->color('success')
                ->icon('heroicon-o-arrow-path')
                ->visible(fn(Post $record) => $record->status === PostStatus::Archived)
                ->form(fn(Post $record) => [
                    RadioList::make('republish_type')
                        ->label('Date de publication')
                        ->options([
                            'keep' => "Conserver la date originale",
                            'now' => 'Maintenant',
                            'schedule' => 'Programmer plus tard',
                        ])
                        ->descriptions([
                            'keep' => 'L\'article sera remis en ligne avec sa date de publication originale.',
                            'now' => 'L\'article sera remis en ligne avec la date et l\'heure actuelles.',
                            'schedule' => 'Choisissez une nouvelle date et heure de publication.',
                        ])
                        ->extras([
                            'keep' => $record->published_at->translatedFormat('l j F Y à H:i'),
                        ])
                        ->default('keep')
                        ->live(),

                    Group::make([
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
                            ->visible(fn($get) => !$get('at_nine_am')) // Visible seulement si case décochée
                            ->default('09:00'),

                        Toggle::make('at_nine_am')
                            ->label('Publier à 09:00')
                            ->default(true)
                            ->live(),

                    ])
                        ->visible(fn($get) => $get('republish_type') === 'schedule'),
                ])
                ->modalSubmitActionLabel('Republier')
                ->action(function (Post $record, array $data) {
                    $publishedAt = now();

                    if ($data['republish_type'] === 'schedule') {
                        // On récupère la date
                        $date = Carbon::parse($data['date_only']);

                        // On définit l'heure
                        if ($data['at_nine_am']) {
                            $date->hour(9)->minute(0)->second(0);
                        } else {
                            $time = Carbon::parse($data['time_only']);
                            $date->hour($time->hour)->minute($time->minute)->second(0);
                        }

                        $publishedAt = $date;
                    }

                    $record->update([
                        'status' => PostStatus::Published,
                        'published_at' => $publishedAt,
                    ]);
                })
                ->after(fn($livewire) => method_exists($livewire, 'refreshFormData') ? $livewire->refreshFormData(['status', 'published_at']) : null),

            Action::make('revert_to_draft')
                ->extraAttributes(['class' => 'w-full']) // <--- Force la largeur 100%
                ->label('Brouillon')
                ->color('gray')
                ->icon('heroicon-o-pencil')
                ->visible(fn(Post $record) => $record->status !== PostStatus::Draft)
                ->requiresConfirmation()
                ->modalHeading('Remettre en brouillon ?')
                ->modalDescription('L\'article sera retiré du site et sa date de publication sera effacée.')
                ->action(function (Post $record) {
                    $record->update([
                        'status' => PostStatus::Draft,
                        'published_at' => null,
                    ]);
                })
                ->after(fn($livewire) => method_exists($livewire, 'refreshFormData') ? $livewire->refreshFormData(['status', 'published_at']) : null),

        ];
    }
}

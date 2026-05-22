<?php

namespace App\Filament\Resources\Events\Pages;

use App\Enums\EventNavigationGroups;
use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Events\Widgets\GalleryHeroWidget;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use TinusG\FilamentHoverImageColumn\HoverImageColumn;
use UnitEnum;

class EditEventGallery extends ManageRelatedRecords
{

    protected static string $resource = EventResource::class;

    protected static string $relationship = 'media';
    protected static string $collection = 'gallery';

    protected static ?string $navigationLabel = 'Galerie';
    protected static ?string $breadcrumb = 'Galerie';
    protected static string|UnitEnum|null $navigationGroup = EventNavigationGroups::Archives;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Galerie';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            GalleryHeroWidget::class
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_name')
            ->reorderable('order_column')
            ->modifyQueryUsing(fn(Builder $query) => $query->where('collection_name', static::$collection))
            ->columns([
                HoverImageColumn::make('url')
                    ->label('Aperçu')
                    ->state(function ($record) {
                        if (! $record->hasGeneratedConversion('thumb'))
                            return $record->getUrl() . '?v=' . $record->updated_at->timestamp;
                        return $record->getUrl('thumb') . '?v=' . $record->updated_at->timestamp;
                    })
                    ->imageWidth('120px')
                    ->imageHeight('auto')
                    ->width('200px')
                    ->grow(false),
                TextColumn::make('name')
                    ->label('Nom')
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('file_name')
                    ->label('Fichier')
                    ->color('gray')
                    ->searchable(),
            ])
            ->headerActions([
                Action::make('upload')
                    ->label('Upload')
                    ->modalDescription('Ajoutez jusq\'a 50 images à la galerie de cet événement. Les images seront automatiquement redimensionnées pour l\'aperçu, mais les fichiers originaux seront conservés.')
                    ->icon(Heroicon::OutlinedCloudArrowUp)
                    ->color('primary')
                    ->schema([
                        FileUpload::make('files')
                            ->multiple()
                            ->hiddenLabel()
                            ->appendFiles()
                            ->panelLayout('grid')
                            ->maxFiles(50)
                            ->image()
                            ->storeFiles(false)
                            ->maxSize(10240),
                    ])
                    ->action(function (array $data, ManageRelatedRecords $livewire) {
                        /** @var \App\Models\Event $event */
                        $event = $livewire->getOwnerRecord();

                        foreach ($data['files'] as $file) {
                            /** @var TemporaryUploadedFile $file */
                            $event->addMedia($file->getRealPath())
                                ->usingName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                                ->toMediaCollection(static::$collection);
                        }
                    })
                    ->modalSubmitActionLabel('Upload')
            ])
            ->recordActions([
                DeleteAction::make()
                    ->iconButton()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                ])
            ]);
    }
}

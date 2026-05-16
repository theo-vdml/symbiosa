<?php

namespace App\Filament\Resources\LegalPages\Pages;

use App\Filament\Resources\LegalPages\LegalPageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditLegalPage extends EditRecord
{
    protected static string $resource = LegalPageResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['content'] = $this->record->latestVersion?->content;
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $newContent = $data['content'] ?? '';
        unset($data['content']);

        $oldContent = $record->latestVersion?->content;

        $record->update($data);

        if ($oldContent !== $newContent) {
            $nextVersion = ($record->latestVersion?->version_number ?? 0) + 1;
            $record->versions()->create([
                'content' => $newContent,
                'version_number' => $nextVersion,
            ]);
        }

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

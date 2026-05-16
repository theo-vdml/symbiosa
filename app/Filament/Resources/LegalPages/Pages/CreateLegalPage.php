<?php

namespace App\Filament\Resources\LegalPages\Pages;

use App\Filament\Resources\LegalPages\LegalPageResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateLegalPage extends CreateRecord
{
    protected static string $resource = LegalPageResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $content = $data['content'] ?? '';
        unset($data['content']);

        $record = static::getModel()::create($data);

        $record->versions()->create([
            'content' => $content,
            'version_number' => 1,
        ]);

        return $record;
    }

    public function canCreateAnother(): bool
    {
        return false;
    }
}

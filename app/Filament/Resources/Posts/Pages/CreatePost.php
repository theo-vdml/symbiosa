<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Posts\Schemas\PostForm;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    public function form(Schema $schema): Schema
    {
        return PostForm::configure($schema, false);
    }

    public function canCreateAnother(): bool
    {
        return false;
    }
}

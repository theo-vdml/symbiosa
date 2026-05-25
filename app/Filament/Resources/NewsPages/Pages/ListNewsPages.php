<?php

namespace App\Filament\Resources\NewsPages\Pages;

use App\Filament\Resources\NewsPages\NewsPageResource;
use Filament\Resources\Pages\ListRecords;

class ListNewsPages extends ListRecords
{
    protected static string $resource = NewsPageResource::class;
}

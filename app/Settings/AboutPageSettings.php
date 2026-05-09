<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AboutPageSettings extends Settings
{
    public ?array $sections;

    public static function group(): string
    {
        return 'about_page';
    }
}

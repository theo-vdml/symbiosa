<?php

namespace App\Settings;

class AboutPageSettings extends PageSettings
{
    public ?array $sections;

    public static function group(): string
    {
        return 'about_page';
    }
}

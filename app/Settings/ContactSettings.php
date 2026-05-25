<?php

namespace App\Settings;

class ContactSettings extends PageSettings
{
    public ?array $email_options;

    public static function group(): string
    {
        return 'contact';
    }
}
